<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    public function setCityNameAttribute($value)
    {

        $this->attributes['city_name'] = $this->cleanUp($value);
    }

    public function setNameOfBusinessAttribute($value)
    {
        $this->attributes['name_of_business'] = $this->cleanUp($value);;
    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = $this->cleanUp($value);;
    }

    public function setYelpListingUrlAttribute($value)
    {
        $this->attributes['yelp_listing_url'] = $this->cleanUp($value);;
    }

    public function setWebsiteAttribute($value)
    {
        $this->attributes['website'] = $this->cleanUp($value);;
    }

    public function setKeywordAttribute($value)
    {
        $this->attributes['keyword'] = $this->cleanUp($value);;
    }

    private function cleanUp($value) {
        $new_string = preg_replace("/[^A-Za-z0-9?!,()\-._ ]/",'',$value);
        $new_string = preg_replace("/ {2,}/", " ", $new_string);
        $new_string = str_replace(' , ', ', ', $new_string);
        $new_string = trim($new_string, ',');
        $new_string = trim($new_string, ' ');

        return $new_string;
    }

}
