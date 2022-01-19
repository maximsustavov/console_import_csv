<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\CustomerModel;

class ImportCsvController extends Controller
{
    public $filename;
    public $delimiter;

    /**
     *
     * @param string $filename  full path file name.
     * @param string $delimiter delimiter csv file.
     */
    public function __construct($filename = '/import/random.csv', $delimiter = ',')
    {
        $this->filename = storage_path() . $filename;
        $this->delimiter = $delimiter;
    }

    /**
     * Validation csv file and insert database.
     *
     * @return array if are have error element.
     */
    public function index()
    {
        $errors_data = [];

        $country_list = GetCountryInfoController::getCountryInfoIso3();

        $rules = [
            'email' => ['required', 'email:rfc,dns', 'unique:customers'],
            'age' => ['required', 'integer', 'between:18,99']
        ];

        $users = $this->csvToArray();

        foreach ($users as $key => $user) {
            $validator = validator::make($user, $rules);
            if ($validator->fails()) {
                //формируем массив с навалидными элементами
                $errors = $validator->errors();
                $errors_data[$key] = $user;

                //формируем строку с невалидными полями
                $error_string = '';
                $all_errors = $errors->get('*');
                foreach ($all_errors as $key_error => $value) {
                    $error_string .= $key_error;
                    if (next($all_errors) == true) {
                        $error_string .= ',';
                    }
                }
                $errors_data[$key]['error'] = $error_string;
            } else {
                //запись в бд
                unset($user['id']);

                $country_code = array_search($user['location'], $country_list);

                if ($country_code) {
                    $user['country_code'] = $country_code;
                } else {
                    $user['location'] = 'Unknown';
                    $user['country_code'] = '';
                }

                $user_ful_name = explode(" ", $user['name']);
                $user['name'] = !empty($user_ful_name[0]) ?? '';
                $user['surname'] = !empty($user_ful_name[1]) ?? '';

                CustomerModel::insert($user);
            }
        }
        return $errors_data;
    }

    /**
     * Convert csv file to array.
     *
     * @return array|false false if error reading file name.
     */
    public function csvToArray()
    {
        if (!file_exists($this->filename) || !is_readable($this->filename)) {
            return false;
        }

        $header = null;
        $data = array();

        if (($handle = fopen($this->filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $this->delimiter)) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }

            }
            fclose($handle);
        }
        return $data;
    }
}
