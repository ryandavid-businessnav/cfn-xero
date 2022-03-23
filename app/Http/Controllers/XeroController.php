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
        //return $this->getOAuth2()->getAuthorizationRedirect();
        return redirect('https://login.xero.com/identity/connect/authorize?response_type=code&code_challenge_method=S256&client_id=F6E5A2767452405A8C69BFC17DDE880D&scope=openid email profile accounting.settings offline_access accounting.contacts&redirect_uri=https://xero.cashflownavfactor.com/xero/callback&state=12345&code_challenge=cgA1VpCZDHQuv58Y6G_uzfPL-WtjvSkNgu6w78bM9nw');
    }

    public function redirectToXero(Request $request){
        $input = $request->all();
        $client = new \GuzzleHttp\Client();

        $response = $client->post('https://identity.xero.com/connect/token', [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => 'F6E5A2767452405A8C69BFC17DDE880D',
                'code' => $input['code'],
                'redirect_uri' => 'https://xero.cashflownavfactor.com/xero/callback',
                'code_verifier' => 'thisismycode123thisismycode123thisismycode123thisismycode1234',
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
        
        // $accessTokens = $this->getOAuth2()->refreshAccessToken($accessToken);
        // dd($body);
        $tenants = $this->getOAuth2()->getTenants($accessToken);
        $selectedTenant = $tenants[0]; // For example purposes, we're pretending the user selected the first tenant.
        //dd(collect($accessToken)->toArray());
        // Step 4 - Store the access token and selected tenant ID against the user's account for future use.
        // You can store these anyway you wish. For this example, we're storing them in the database using Eloquent.
        // $user = auth()->user();
        // $user->xero_access_token = json_encode($accessToken);
        // $user->tenant_id = $selectedTenant->tenantId;
        // $user->save();
        //dd($selectedTenant->tenantId);

        // foreach($tenants as $key => $tenant){
        //     $xeroCheck = DB::table('business_setgings')->where(['xero_tenant_id' => $tenant->tenantId])->first();
        //     if(collect($xeroCheck)->isEmpty()){
                // $xero = new XeroApp(
                //     new AccessToken(collect($accessToken)->toArray()),
                //     $tenant->tenantId
                // );
        //         $userOrg = new UserOrganization();
        //         $userOrg->user_id = auth()->user()->id;
        //         $userOrg->xero_access_token = json_encode($accessToken);
        //         $userOrg->tenant_id = $tenant->tenantId;
        //         $userOrg->org_name = $xero->organisations->first()->Name;
        //         $userOrg->save();
                
        //         DB::table('user_to_organizations')->insert([
        //             'user_id' => auth()->user()->id,
        //             'org_id' => $userOrg->id,
        //             'role' => 'owner'
        //         ]);
                
        //     }else{
        //         DB::table('user_to_organizations')->insert([
        //             'user_id' => auth()->user()->id,
        //             'org_id' => $xeroCheck->id,
        //             'role' => 'member'
        //         ]);
        //     }
        // }
        foreach($tenants as $key => $tenant){

            $xero = new XeroApp(
                new AccessToken(collect($accessToken)->toArray()),
                $tenant->tenantId
            );
            // $xeroCheck = DB::table('business_settings')->where(['xero_tenant_id' => $tenant->tenantId])->first();
            // if(collect($xeroCheck)->isEmpty()){
                $org = $xero->organisations()->first();
                $user = $xero->users()->first();
                //dd($user);
                // $contact = $xero->contacts()->find($tenant->id);
                // dd($contact);
                $request->session()->put('xeroOrg', collect($org)->toArray());
                $request->session()->put('tenantId', $tenant->tenantId);
                $request->session()->put('tenantInfo', collect($tenant)->toArray());
                $request->session()->put('userInfo', collect($user)->toArray());
                $request->session()->put('accessToken', collect($accessToken)->toArray());

                return redirect('/home')->with('status', 'Organisation update success!');
            // }else{
            //     return redirect('/home')->with('status', 'Organisation already exist!');
            // }
        }

        dd($accessToken);
        //dd($accessToken->hasExpired());
        if ($accessToken->hasExpired()) {
            $accessTokens = $this->getOAuth2()->refreshAccessToken($accessToken);
            
            $request->session()->forget('access_token');
            $request->session()->put('access_token', json_encode($accessTokens));
            //dd($request->session()->get('access_token'));
            DB::table('user_organizations')->where('tenant_id', $request->session()->get('xeroOrg')->tenant_id)->update([
                'xero_access_token' => json_encode($accessTokens)
            ]);
        }

        $tenants = $this->getOAuth2()->getTenants(collect($body));
            dd($tenants);

        if ($body = $response->getBody()) {
            echo '1234';
            echo "<br/>";

            
            // Cast to a string: { ... }
            $body->seek(0);
            // Rewind the body
            $body->read(1024);
            // Read bytes of the body
        }
        dd($body);
        
        dd($response->getBody());
        dd($input);
    }

    public function test(){
        $client = new \GuzzleHttp\Client();

        $response = $client->post('https://identity.xero.com/connect/token', [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => 'F6E5A2767452405A8C69BFC17DDE880D',
                'code' => '8c7b5afb7a1d21726f67b57b8801fd148a0a7f6b1f9f1d7a71f9a0542863e71a',
                'redirect_uri' => 'https://xero.cashflownavfactor.com',
                'code_verifier' => 'thisismycode123thisismycode123thisismycode123thisismycode1234',
            ]
        ]);
        if ($body = $response->getBody()) {
            echo $body;
            // Cast to a string: { ... }
            $body->seek(0);
            // Rewind the body
            $body->read(1024);
            // Read bytes of the body
            dd($body);
        }
        dd($response->getBody());
    }

    public function handleCallbackFromXero(Request $request)
    {
        // Step 2 - Capture the response from Xero, and obtain an access token.
        
        $accessToken = $this->getOAuth2()->getAccessTokenFromXeroRequest($request);
        dd(collect($accessToken)->toArray());
        dd($accessToken);
        // Step 3 - Retrieve the list of tenants (typically Xero organisations), and let the user select one.
        $tenants = $this->getOAuth2()->getTenants($accessToken);
        $selectedTenant = $tenants[0]; // For example purposes, we're pretending the user selected the first tenant.
        //dd(collect($accessToken)->toArray());
        // Step 4 - Store the access token and selected tenant ID against the user's account for future use.
        // You can store these anyway you wish. For this example, we're storing them in the database using Eloquent.
        // $user = auth()->user();
        // $user->xero_access_token = json_encode($accessToken);
        // $user->tenant_id = $selectedTenant->tenantId;
        // $user->save();
        //dd($selectedTenant->tenantId);

        // foreach($tenants as $key => $tenant){
        //     $xeroCheck = DB::table('business_setgings')->where(['xero_tenant_id' => $tenant->tenantId])->first();
        //     if(collect($xeroCheck)->isEmpty()){
                // $xero = new XeroApp(
                //     new AccessToken(collect($accessToken)->toArray()),
                //     $tenant->tenantId
                // );
        //         $userOrg = new UserOrganization();
        //         $userOrg->user_id = auth()->user()->id;
        //         $userOrg->xero_access_token = json_encode($accessToken);
        //         $userOrg->tenant_id = $tenant->tenantId;
        //         $userOrg->org_name = $xero->organisations->first()->Name;
        //         $userOrg->save();
                
        //         DB::table('user_to_organizations')->insert([
        //             'user_id' => auth()->user()->id,
        //             'org_id' => $userOrg->id,
        //             'role' => 'owner'
        //         ]);
                
        //     }else{
        //         DB::table('user_to_organizations')->insert([
        //             'user_id' => auth()->user()->id,
        //             'org_id' => $xeroCheck->id,
        //             'role' => 'member'
        //         ]);
        //     }
        // }
        foreach($tenants as $key => $tenant){

            $xero = new XeroApp(
                new AccessToken(collect($accessToken)->toArray()),
                $tenant->tenantId
            );
            // $xeroCheck = DB::table('business_settings')->where(['xero_tenant_id' => $tenant->tenantId])->first();
            // if(collect($xeroCheck)->isEmpty()){
                $org = $xero->organisations()->first();
                $user = $xero->users()->first();
                //dd($user);
                // $contact = $xero->contacts()->find($tenant->id);
                // dd($contact);
                $request->session()->put('xeroOrg', collect($org)->toArray());
                $request->session()->put('tenantId', $tenant->tenantId);
                $request->session()->put('tenantInfo', collect($tenant)->toArray());
                $request->session()->put('userInfo', collect($user)->toArray());
                $request->session()->put('accessToken', collect($accessToken)->toArray());

                return redirect('/home')->with('status', 'Organisation update success!');
            // }else{
            //     return redirect('/home')->with('status', 'Organisation already exist!');
            // }
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