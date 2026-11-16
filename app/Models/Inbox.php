<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'phone','subject','company_name','message','category','role','location','topic','reason','social_media_url','site_url','resume_url','job_id','job_title','status'])]

class Inbox extends Model
{

}
