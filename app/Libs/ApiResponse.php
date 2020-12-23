<?php


namespace App\Libs;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Response;

class ApiResponse
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
    /**
     * @var array
     */
    private $links = null;
    /**
     * @var array
     */
    private $meta = null;

    function __construct(){
    }

    /**
     * Create new MyResponse instance.
     *
     * @return ApiResponse instance
     */
    public static function make(){
        return new ApiResponse();
    }

    /**
     * Add data to be returned in json response
     * set http status to 200
     *
     * @param  string|array|Collection  $data
     * @return $this
     */
    public function data($data){
        $this->data = $data;
        $this->httpStatus = 200;
        $this->appCode = '00';
        return $this;
    }

    /**
     * Add pagination  in json response
     * set http status to 200
     *
     * @param LengthAwarePaginator $paginator
     * @return $this
     */
    public function paginator(LengthAwarePaginator $paginator){
        $this->data = $paginator->all();
        $this->httpStatus = 200;
        $this->appCode = '00';

        $this->links($paginator);
        $this->meta($paginator);

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
     * @param  string|array|Collection  $data
     * @return $this
     */
    public function isCreated($data=null){
        $this->message = 'Berhasil menambahkan data baru';
        $this->appCode = '00';
        $data ? $this->data = $data : $this->data;
        $this->httpStatus = 201;
        return $this;
    }

    /**
     * Set http response to 200 (created)
     * @param  string|array|Collection  $data
     * @return $this
     */
    public function isUpdated($data=null){
        $this->message = 'Berhasil memperbaharui data';
        $this->appCode = '00';
        $this->httpStatus = 200;
        $data ? $this->data = $data : $this->data;
        return $this;
    }

    /**
     * Set http response to 400 (bad request)
     * @param String $message
     * @return $this
     */
    public function isNotAllowed($message=null){
        $this->message = $message ?? 'Permintaan anda ditolak';
        $this->appCode = '04';
        $this->httpStatus = 400;
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
     * @return JsonResponse
     */
    public function json(){
        $response = [
            'code' => $this->appCode,
            'message' => $this->message,
            'data'   => $this->data
        ];

        if($this->errors){$response['errors'] = $this->errors;}
        if($this->links && $this->meta){$response['paginator']=[
            'links' => $this->links,
            'meta'  => $this->meta
        ];}

        return Response::json($response,$this->httpStatus);
    }

    private function links(LengthAwarePaginator $paginator){
        $this->links = [
            'first'=> "{$paginator->url(1)}",
            'last' => "{$paginator->url($paginator->lastPage())}",
            'prev' => "{$paginator->url(($paginator->currentPage() - 1))}",
            'next' => "{$paginator->url(($paginator->currentPage() + 1))}"
        ];
    }

    private function meta(LengthAwarePaginator $paginator)
    {
        $this->meta = [
            'current_page'=> $paginator->currentPage(),
            'last_page'   => $paginator->lastPage(),
            'path'        => $paginator->path(),
            'per_page'    => $paginator->perPage(),
            'from'        => $paginator->firstItem(),
            'to'          => $paginator->lastItem(),
            'total'       => $paginator->total()
        ];
    }

}
