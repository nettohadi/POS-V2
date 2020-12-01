<?php


namespace App\Lib;

class MyResponse
{

    private $data = null;
    private $errors = null;
    /**
     * @var string
     */
    private $appCode = '00';
    /**
     * @var int
     */
    private $httpStatus = 200;
    /**
     * @var string
     */
    private $message = 'Berhasil menampilkan data';

    function __construct(){
    }

    /**
     * Create new MyResponse instance.
     *
     * @return MyResponse instance
     */
    public static function make(){
        return new MyResponse();
    }

    /**
     * Add data to be returned in json response
     * set http status to 200
     *
     * @param  string|array  $data
     * @return $this
     */
    public function data($data){
        $this->data = $data;
        $this->httpStatus = 200;
        $this->appCode = '00';
        return $this;
    }

    /**
     * Set http status to 400 (Bad request).
     * Add errors if exist.
     *
     * @param  string|array  $errors
     * @return $this
     */
    public function isNotValid($errors=null){
        $this->message = 'Data yang anda kirim tidak valid';
        $this->appCode = '01';
        $this->httpStatus = 400;
        $this->errors = $errors;
        return $this;
    }

    /**
     * Set http status to 404 (Not Found).
     *
     * @return $this
     */
    public function isNotFound(){
        $this->message = 'Data tidak ditemukan';
        $this->appCode = '03';
        $this->httpStatus = 404;
        return $this;
    }

    /**
     * Set http response to 201 (created)
     *
     * @return $this
     */
    public function isCreated(){
        $this->message = 'Berhasil menambahkan data baru';
        $this->appCode = '00';
        $this->httpStatus = 201;
        return $this;
    }

    /**
     * Set http response to 200 (created)
     *
     * @return $this
     */
    public function isUpdated(){
        $this->message = 'Berhasil memperbaharui data';
        $this->appCode = '00';
        $this->httpStatus = 200;
        return $this;
    }

    /**
     * Set http response to 200 (created)
     *
     * @return $this
     */
    public function isDeleted(){
        $this->message = 'Berhasil menghapus data';
        $this->appCode = '00';
        $this->httpStatus = 200;
        return $this;
    }

    /**
     * Add errors to be returned in json response
     *
     * @param  string|array  $errors
     * @return $this
     */
    public function errors($errors){
        $this->errors = $errors;
        return $this;
    }

    /**
     * Create a new JSON response instance.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(){
        $response = [
            'code' => $this->appCode,
            'message' => $this->message,
            'data'   => $this->data
        ];

        if($this->errors){$response['errors'] = $this->errors;}

        return response()->json($response,$this->httpStatus);
    }
}
