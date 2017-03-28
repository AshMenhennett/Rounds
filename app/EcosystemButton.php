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
     * Whether or not a button has a file associated with it.
     *
     * @return boolean
     */
    public function hasFile()
    {
        return $this->file_name !== null;
    }

    /**
     * Gets the file extension of a button's file or null, if the button doesn't have a file associated.
     *
     * @return string | null
     */
    public function getFileExtension()
    {
        if ($this->hasFile()) {
            return substr($this->file_name, (strrpos($this->file_name, '.') + 1));
        }
        return null;
    }

    /**
     * Returns whether or not a file has the pdf extension.
     *
     * @return boolean
     */
    public function hasPDFFile()
    {
        return $this->getFileExtension() === 'pdf';
    }

    /**
     * Returns a buttons file name, if it has one, otherwise, return null
     *
     * @return string | null
     */
    public function getFileName()
    {
        if ($this->hasFile()) {
            return $this->file_name;
        }
        return null;
    }

    /**
     * Whether or not a button has a link associated with it.
     *
     * @return boolean
     */
    public function hasLink()
    {
        return $this->link !== null;
    }

    /**
     * Returns a buttons link url, if it has one, otherwise, return null
     *
     * @return string | null
     */
    public function getLink()
    {
        if ($this->hasLink()) {
            return $this->link;
        }
        return null;
    }

}
