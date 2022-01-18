<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Models\CustomerModel;
use App\Http\Controllers\get_country_info_controller;

class import_csv_controller extends Controller
{
    public function index()
    {
        $errors_data = [];

        $country_list = get_country_info_controller::get_country_info_iso_3();

        $rules = [
            'email' => ['required','email:rfc,dns','unique:customers'],
            'age' => ['required','integer','between:18,99']
        ];

        $path = storage_path () . '/import/random.csv';
        $users = $this->csv_to_array($path);


        foreach ($users as $key => $user) {
            $validator = validator::make($user,$rules);
            if($validator->fails()){
                //формируем массив с навалидными элементами
                $errors = $validator->errors();
                $errors_data[$key] = $user;

                //формируем строку с невалидными полями
                $error_string = '';
                $all_errors = $errors->get('*');
                foreach ($all_errors as $key_error => $value) {
                    $error_string .= $key_error;
                    if(next($all_errors)==true)
                        $error_string .= ',';
                }
                $errors_data[$key]['error'] = $error_string;
            } else {
                //запись в бд
                unset($user['id']);

                $country_code = array_search($user['location'],$country_list);

                if($country_code){
                    $user['country_code'] = $country_code;
                } else {
                    $user['location'] = 'Unknown';
                    $user['country_code'] = '';
                }

                $user_ful_name = explode(" ", $user['name']);
                $user['name'] = !empty($user_ful_name[0])?$user_ful_name[0]:'';
                $user['surname'] = !empty($user_ful_name[1])?$user_ful_name[1]:'';

                CustomerModel::insert($user);
            }
        }
        return $errors_data;
    }

    public function csv_to_array($filename = '', $delimiter = ',')
    {
		if (!file_exists($filename) || !is_readable($filename))
			return FALSE;

		$header = NULL;
		$data = array();
		if (($handle = fopen($filename, 'r')) !== FALSE) {
			while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
				if (!$header)
					$header = $row;
				else
					$data[] = array_combine($header, $row);
			}
			fclose($handle);
		}
		return $data;
	}

}
