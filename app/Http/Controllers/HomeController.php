<?php

namespace App\Http\Controllers;

use LangleyFoxall\XeroLaravel\OAuth2;
use LangleyFoxall\XeroLaravel\XeroApp;
use League\OAuth2\Client\Token\AccessToken;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\BusinessSetting;
use App\Models\Organisation;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    private function getOAuth2()
    {
        // This will use the 'default' app configuration found in your 'config/xero-laravel-lf.php` file.
        // If you wish to use an alternative app configuration you can specify its key (e.g. `new OAuth2('other_app')`).
        return new OAuth2();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function formatNum($num){
        return sprintf("%+d",$num);
    }

    public function index(Request $request)
    {
        //dd( $request->session()->get('xeroOrg.Phones.0.PhoneNumber'));
        //dd(($request->session()->get('accessToken.id_token')));
        if($request->session()->get('accessToken.id_token')){
            $jsonToken = $request->session()->get('accessToken.id_token');
            $token = ($jsonToken);
            
            $tokenParts = explode(".", $token);  
            $tokenHeader = base64_decode($tokenParts[0]);
            $tokenPayload = base64_decode($tokenParts[1]);

            $jwtHeader = json_decode($tokenHeader);
            $jwtPayload = json_decode($tokenPayload);
            
            $phoneNumber = str_replace(' ', '', session('xeroOrg.Phones.0.PhoneNumber'));

            
            $request->session()->put('phoneNumber', $phoneNumber);
            $request->session()->put('jwtPayload', collect($jwtPayload)->toArray());
        }

        //dd($request->session()->get('jwtPayload'));
        // $contact = $xero->contacts()->find('34xxxx6e-7xx5-2xx4-bxx5-6123xxxxea49');
        //$tenants = $this->getOAuth2()->getTenants(json_encode($request->session()->get('accessToken')));
        //dd($tenants);
        return view('home');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'max:20', 'confirmed'],
        ]);
    }

    public function refreshXeroToken(Request $request){
        //dd($request->session()->get('access_token'));
        //refresh token if necessary
        $accessToken = new AccessToken(collect(json_decode( json_encode($request->session()->get('accessToken')) ))->toArray());
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
    }

    public function saveUser(Request $request){
        $input = $request->all();

        $request->session()->put('access_token', $request->session()->get('accessToken.access_token'));

        if(collect($request->session()->get('accessToken'))->isEmpty() ){
            return redirect('/home');
        }else{
            $this->refreshXeroToken($request);
        }

        $convertedUserPhone = '';
        $convertedPhone = '';

            //dd($input['phoneNumber']);
        if(collect($input['userPhoneNumber'])->isNotEmpty()){
            if($input['userPhoneNumber'][0] == "0"){
                $convertedUserPhone = substr_replace($input['userPhoneNumber'], '+61', 0, strlen("0"));
            }else{
                $convertedUserPhone = '+61'.$input['userPhoneNumber'];
            }
        }
        if(collect($input['phoneNumber'])->isNotEmpty()){
            if($input['phoneNumber'][0] == "0"){
                $convertedPhone = substr_replace($input['phoneNumber'], '+61', 0, strlen("0"));
                //$convertedPhone = substr($input['userPhoneNumber'],"+61",0);
            }else{
                $convertedPhone = '+61'.$input['phoneNumber'];
            }
        }

        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        //dd($convertedUserPhone);
        $phoneCheck = DB::table('users')->where('mobile_number', $convertedUserPhone)->first();
        if(collect($phoneCheck)->isNotEmpty()){
            return redirect('/home')->with('error_status', 'Mobile number already exist!');
        }
        $emailCheck = DB::table('users')->where('email', $input['email'])->first();
        if(collect($emailCheck)->isNotEmpty()){
            return redirect('/home')->with('error_status', 'Email already exist!');
        }
        $user = new User();
        $user->email = $input['email'];
        $user->password = Hash::make($input['password']);
        $user->first_name = $input['firstName'];
        $user->last_name = $input['lastName'];
        $user->mobile_number = $convertedUserPhone;
        $user->is_active = 1;
        $user->is_verified = 0;
        $user->is_phone_verified = 0;
        $user->xero_userid = $request->session()->get('jwtPayload.xero_userid');
        $user->save();

        $xeroCheck = DB::table('business_settings')->where(['xero_tenant_id' => $request->session()->get('tenantId')])->first();
        //if(collect($xeroCheck)->isEmpty()){

        

            $businessSetting = new BusinessSetting();
            $businessSetting->user_id = $user->id;
            $businessSetting->company_name = $input['businessName'];
            $businessSetting->address = $input['address'];
            $businessSetting->no_of_employees = $input['numberOfEmployees'];
            $businessSetting->currency = $input['currency'];
            $businessSetting->anzic_division = $input['anzic_code'];
            $businessSetting->anzic_subdivision = $input['anzic_subdivision'];
            $businessSetting->mobile_number = $convertedPhone;
            $businessSetting->trading_name = $input['businessName'];
            $businessSetting->xero_access_token = $request->session()->get('accessToken.access_token');
            $businessSetting->xero_refresh_token = $request->session()->get('accessToken.refresh_token');
            $businessSetting->xero_tenant_name = $request->session()->get('tenantInfo.tenantName');
            $businessSetting->xero_tenant_id = $request->session()->get('tenantId');
            $businessSetting->save();

            $org = new Organisation();
            $org->business_form_id = $businessSetting->id;
            $org->user_id = $user->id;
            $org->role = 'owner';
            $org->read_access = 1;
            $org->write_access = 1;
            $org->is_admin = 1;
            $org->save();
        // }else{
        //     $org = new Organisation();
        //     $org->business_form_id = $xeroCheck->id;
        //     $org->user_id = $user->id;
        //     $org->role = 'member';
        //     $org->read_access = 1;
        //     $org->write_access = 1;
        //     $org->is_admin = 1;
        //     $org->save();
        // }

        $request->session()->forget('xeroOrg');
        $request->session()->forget('tenantInfo');
        $request->session()->forget('tenantId');
        $request->session()->forget('userInfo');
        $request->session()->forget('accessToken');
        $request->session()->forget('access_token');
        $request->session()->forget('jwtPayload');
        $request->session()->forget('phoneNumber');

        return redirect('/home')->with('status', 'User register success! Download CASHFLOWNAV on iOS or Android devices to Sign in!');
    }
}
