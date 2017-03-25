<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EcosystemButton extends Model
{

    /**
     * Mass assignable properties.
     * @var array
     */
    protected $fillable = ['value', 'link', 'file_name'];

    /**
     * Destination URL of button.
     * Is URL to file (if available) or URL to external site.
     *
     * @return string
     */
    public function destination()
    {
        if ($this->file_name !== null) {
            return '/storage/' . $this->file_name;
        }
        return $this->link;
    }

    /**
     * Whether or not a button has a file associated with it.
     *
     * @return boolean
     */
    public function hasFile()
    {
        return $this->file_name !== null;
    }

}
