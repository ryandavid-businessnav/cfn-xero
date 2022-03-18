<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model;
    class BusinessSetting extends Model
    {
        protected $table = 'business_settings';

        protected $fillable = [
	        'user_id','company_name','address','no_of_employees','currency','trading_name','xero_refresh_token','xero_tenant_id','xero_tenant_name'
	    ];
    }
?>