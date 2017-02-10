<?php

namespace App\Http\Requests\Admin;

use App\Team;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // get segements for current request path
        $segments = $this->segments();
        // get the team through the team slug, index 2 in array returned from $this->segments()
        $team = Team::where('slug', $segments[2])->first();

        // TODO TEST
        return [
            'name' => 'required|unique:teams,name,'. $team->id .'|max:255',
            'slug' => 'required|unique:teams,slug,'. $team->id .'|max:255',
        ];
    }
}
