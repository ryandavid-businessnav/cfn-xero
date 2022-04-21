<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use LangleyFoxall\XeroLaravel\OAuth2;
use LangleyFoxall\XeroLaravel\XeroApp;
use League\OAuth2\Client\Token\AccessToken;
use App\Models\UserOrganization;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class XeroController extends Controller
{
    private function getOAuth2()
    {
        // This will use the 'default' app configuration found in your 'config/xero-laravel-lf.php` file.
        // If you wish to use an alternative app configuration you can specify its key (e.g. `new OAuth2('other_app')`).
        return new OAuth2();
    }

    public function redirectUserToXero()
    {
        // Step 1 - Redirect the user to the Xero authorization URL.

        $code_verifier = '--6t5HeyDNhPx8C9MYOEFWAgj9q9Ijhg7at-WtGGmrgIVB';
        $hash = hash('sha256', $code_verifier);
        $code_challenge = base64_encode(pack('H*', $hash));
        $codeChallenge = strtr(rtrim($code_challenge, '='), '+/', '-_');

        return redirect('https://login.xero.com/identity/connect/authorize?response_type=code&code_challenge_method=S256&client_id=F6E5A2767452405A8C69BFC17DDE880D&scope=openid email profile accounting.settings offline_access accounting.contacts&redirect_uri=https://xero.cashflownavfactor.com/xero/callback&state=12345&code_challenge='.$codeChallenge);

        //return redirect('https://login.xero.com/identity/connect/authorize?response_type=code&code_challenge_method=S256&client_id=F6E5A2767452405A8C69BFC17DDE880D&scope=openid email profile accounting.settings offline_access accounting.contacts&redirect_uri=http://localhost:8001/xero/callback&state=12345&code_challenge='.$codeChallenge);
    }

    public function redirectToXero(Request $request){
        $input = $request->all();
        $client = new \GuzzleHttp\Client();
        if(isset($input['error']) && $input['error'] == "access_denied"){
            return redirect('/home');
        }

        $code_verifier = '--6t5HeyDNhPx8C9MYOEFWAgj9q9Ijhg7at-WtGGmrgIVB';

        $response = $client->post('https://identity.xero.com/connect/token', [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => 'F6E5A2767452405A8C69BFC17DDE880D',
                'code' => $input['code'],
                'redirect_uri' => 'https://xero.cashflownavfactor.com/xero/callback',
                //'redirect_uri' => 'http://localhost:8001/xero/callback',
                //'code_verifier' => 'thisismycode123thisismycode123thisismycode123thisismycode1234',
                'code_verifier' => $code_verifier
            ]
        ]);
        $resp = $response->getBody();

        $result = json_decode($resp, true);

        $body = [
            'id_token' => $result['id_token'],
            'token_type' => 'Bearer',
            'scope' => 'openid email profile accounting.settings offline_access accounting.contacts',
            'access_token' => $result['access_token'],
            'refresh_token' => $result['refresh_token'],
            'expires' => $result['expires_in']
        ];

        $accessToken = new AccessToken(collect(json_decode( json_encode($body) ))->toArray());
        $tenants = $this->getOAuth2()->getTenants($accessToken);
        $selectedTenant = $tenants[0]; // For example purposes, we're pretending the user selected the first tenant.
        foreach($tenants as $key => $tenant){

            $xero = new XeroApp(
                new AccessToken(collect($accessToken)->toArray()),
                $tenant->tenantId
            );
            // $xeroCheck = DB::table('business_settings')->where(['xero_tenant_id' => $tenant->tenantId])->first();
            $org = $xero->organisations()->first();
            $user = $xero->users()->first();
            $request->session()->put('xeroOrg', collect($org)->toArray());
            $request->session()->put('tenantId', $tenant->tenantId);
            $request->session()->put('tenantInfo', collect($tenant)->toArray());
            $request->session()->put('userInfo', collect($user)->toArray());
            $request->session()->put('accessToken', collect($accessToken)->toArray());

            return redirect('/home')->with('status', 'Successfully Signed in via Xero! Fill out the form to register to CASHFLOWNAV!');
        }
    }

    public function refreshAccessTokenIfNecessary(Request $request)
    {
        // Step 5 - Before using the access token, check if it has expired and refresh it if necessary.
        $user = auth()->user();
        $accessToken = new AccessToken(collect(json_decode($request->session()->get('xeroOrg')->xero_access_token))->toArray());
        //dd($request->session()->get('xeroOrg')->tenant_id);
        if ($accessToken->hasExpired()) {
            $accessToken = $this->getOAuth2()->refreshAccessToken($accessToken);

            DB::table('user_organizations')->update([
                'xero_access_token' => json_encode($accessToken)
            ])->where('tenant_id', $request->session()->get('xeroOrg')->tenant_id);
        }
    }
}