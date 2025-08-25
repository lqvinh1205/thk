<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateHotelRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'hotel_name' => [
				'required',
				'string',
				'max:255',
				'min:2',
			],
			'prefecture_id' => [
				'required',
				'integer',
				'exists:prefectures,prefecture_id'
			],
			'hotel_image' => [
				'nullable',
				'image',
				'mimes:jpeg,jpg,png',
				'max:5120' // 5MB in kilobytes
			],
		];
	}

	/**
	 * Get custom error messages for validation rules.
	 *
	 * @return array<string, string>
	 */
	public function messages(): array
	{
		return [
			'hotel_name.required' => 'ホテル名は必須です。',
			'hotel_name.string' => 'ホテル名は文字列で入力してください。',
			'hotel_name.max' => 'ホテル名は255文字以内で入力してください。',
			'hotel_name.min' => 'ホテル名は2文字以上で入力してください。',

			'prefecture_id.required' => '都道府県を選択してください。',
			'prefecture_id.integer' => '都道府県の選択が無効です。',
			'prefecture_id.exists' => '選択された都道府県が存在しません。',

			'hotel_image.image' => 'ホテル画像は画像ファイルである必要があります。',
			'hotel_image.mimes' => 'ホテル画像はJPEG、JPG、PNG形式のファイルをアップロードしてください。',
			'hotel_image.max' => 'ホテル画像のファイルサイズは5MB以下にしてください。',
		];
	}

	/**
	 * Get custom attributes for validator errors.
	 *
	 * @return array<string, string>
	 */
	public function attributes(): array
	{
		return [
			'hotel_name' => 'ホテル名',
			'prefecture_id' => '都道府県',
			'hotel_image' => 'ホテル画像',
		];
	}
}
