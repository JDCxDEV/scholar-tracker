<?php

namespace App\Http\Requests\Admin\Scholars;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\ValidationException;

class ScholarTypeStoreRequest extends FormRequest
{
    protected $id;

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
        $this->id = $this->route('id');

        if($this->input('type') == 'quota' && (!$this->filled('slp_required') || !$this->filled('frequency'))){
            $error = ValidationException::withMessages([
                'slp_required' => ['Slp requirement is required field'],
                'frequency' => ['Set slp Requirement is requred field'],
             ]);
             throw $error;
        }


        if($this->input('type') == 'percentage' && (!$this->filled('manager_share') || !$this->filled('scholar_share'))){
            $error = ValidationException::withMessages([
                'manager_share' => ['Scholar share is required field'],
                'scholar_share' => ['Manager share is requred field'],
             ]);
             throw $error;
        }

        return $this->getRules();

    }

    public function messages()
    {
        return [

        ];
    }

    protected function getRules() {
        return [
            'name' => ['required'],
            'description' => ['required'],
            'type' => ['required'],
        ];
    }
}
