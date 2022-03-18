<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model;
    class Organisation extends Model
    {
        protected $table = 'organisations';

        protected $fillable = [
	        'business_id','user_id','role','read_access','write_access','is_admin'
	    ];
    }
?>