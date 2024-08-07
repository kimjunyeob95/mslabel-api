<?php

namespace App\Http\Controllers\Admin;

use App\Constants\HttpConstant;
use App\Constants\MainErrorMessageConstant;
use App\Http\Controllers\Controller;
use App\Services\Admin\MainService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class MainController extends Controller
{
    private Request $request;
    private MainService $mainService;

    function __construct(Request $request, MainService $mainService)
    {
        $this->request     = $request;
        $this->mainService = $mainService;
    }

    function topBannersList(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'page'      => 'required|int',
                'page_size' => 'required|string',
            ], [
                'page.required'      => MainErrorMessageConstant::getNotHaveErrorMessage("PAGE"),
                'page_size.required' => MainErrorMessageConstant::getNotHaveErrorMessage("PAGE_SIZE"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $page      = $this->request->get("page", 1);
            $pageSize  = $this->request->get("page_size", 50);
            $isShow    = $this->request->get("is_show", "") ?? "";
            $searchCls = $this->request->get("search_cls", "") ?? "";
            $keyword   = $this->request->get("keyword", "") ?? "";
            $sort      = $this->request->get("sort", "created_at|desc") ?? "created_at|desc";

            if( $pageSize > 50 ) $pageSize = 50;

            $params = [
                'page'      => $page,
                'pageSize'  => $pageSize,
                'isShow'    => $isShow,
                'searchCls' => $searchCls,
                'keyword'   => $keyword,
                'sort'      => $sort,
            ];

            $result = $this->mainService->topBannersList($params);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                throw new Exception($result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    function topBannerCreate(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'title'           => 'required|string',
                'is_always_show'  => 'required|string',
                'show_started_at' => 'required|date_format:Y-m-d',
                'show_ended_at'   => 'required|date_format:Y-m-d',
                'is_show'         => 'required|string',
                'thumbnail'       => 'required|file',
            ], [
                'title.required'              => MainErrorMessageConstant::getNotHaveErrorMessage("TITLE"),
                'is_always_show.required'     => MainErrorMessageConstant::getNotHaveErrorMessage("IS_ALWAYS_SHOW"),
                'show_started_at.required'    => MainErrorMessageConstant::getNotHaveErrorMessage("SHOW_STARTED_AT"),
                'show_started_at.date_format' => MainErrorMessageConstant::getFitErrorMessage("SHOW_STARTED_AT_FORMAT"),
                'show_ended_at.required'      => MainErrorMessageConstant::getNotHaveErrorMessage("SHOW_ENDED_AT"),
                'show_ended_at.date_format'   => MainErrorMessageConstant::getFitErrorMessage("SHOW_ENDED_AT_FORMAT"),
                'is_show.required'            => MainErrorMessageConstant::getNotHaveErrorMessage("IS_SHOW"),
                'thumbnail.required'          => MainErrorMessageConstant::getNotHaveErrorMessage("THUMBNAIL"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $thumbnail = $this->request->file('thumbnail');
            if (strpos($thumbnail->getClientMimeType(), 'image') !== 0) {
                throw new Exception(MainErrorMessageConstant::getFitErrorMessage("THUMBNAIL_TYPE"));
            }
            // 파일 크기 검증 (2MB 이하)
            if ($thumbnail->getSize() > 2 * 1024 * 1024) {
                throw new Exception(MainErrorMessageConstant::getFitErrorMessage("THUMBNAIL_SIZE_2MB"));
            }
            // 이미지 크기 검증 (1920x560)
            list($width, $height) = getimagesize($thumbnail->getPathname());
            if ($width > 1920 || $height > 560) {
                throw new Exception(MainErrorMessageConstant::getFitErrorMessage("THUMBNAIL_1920_560"));
            }

            $title         = $this->request->post('title');
            $isAlwaysShow  = $this->request->post('is_always_show');
            $showStartedAt = $this->request->post('show_started_at');
            $showEndedAt   = $this->request->post('show_ended_at');
            $isShow        = $this->request->post('is_show');

            $params = [
                'title'         => $title,
                'isAlwaysShow'  => $isAlwaysShow,
                'showStartedAt' => $showStartedAt,
                'showEndedAt'   => $showEndedAt,
                'isShow'        => $isShow,
                'thumbnail'     => $thumbnail,
            ];

            $result = $this->mainService->topBannerCreate($params);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                throw new Exception($result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    function topBannerEdit(int $id): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'title'           => 'required|string',
                'is_always_show'  => 'required|string',
                'show_started_at' => 'required|date_format:Y-m-d',
                'show_ended_at'   => 'required|date_format:Y-m-d',
                'is_show'         => 'required|string',
                'thumbnail'       => 'required|file',
            ], [
                'title.required'              => MainErrorMessageConstant::getNotHaveErrorMessage("TITLE"),
                'is_always_show.required'     => MainErrorMessageConstant::getNotHaveErrorMessage("IS_ALWAYS_SHOW"),
                'show_started_at.required'    => MainErrorMessageConstant::getNotHaveErrorMessage("SHOW_STARTED_AT"),
                'show_started_at.date_format' => MainErrorMessageConstant::getFitErrorMessage("SHOW_STARTED_AT_FORMAT"),
                'show_ended_at.required'      => MainErrorMessageConstant::getNotHaveErrorMessage("SHOW_ENDED_AT"),
                'show_ended_at.date_format'   => MainErrorMessageConstant::getFitErrorMessage("SHOW_ENDED_AT_FORMAT"),
                'is_show.required'            => MainErrorMessageConstant::getNotHaveErrorMessage("IS_SHOW"),
                'thumbnail.required'          => MainErrorMessageConstant::getNotHaveErrorMessage("THUMBNAIL"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $thumbnail = $this->request->file('thumbnail');
            if (strpos($thumbnail->getClientMimeType(), 'image') !== 0) {
                throw new Exception(MainErrorMessageConstant::getFitErrorMessage("THUMBNAIL_TYPE"));
            }
            // 파일 크기 검증 (2MB 이하)
            if ($thumbnail->getSize() > 2 * 1024 * 1024) {
                throw new Exception(MainErrorMessageConstant::getFitErrorMessage("THUMBNAIL_SIZE_2MB"));
            }
            // 이미지 크기 검증 (1920x560)
            list($width, $height) = getimagesize($thumbnail->getPathname());
            if ($width > 1920 || $height > 560) {
                throw new Exception(MainErrorMessageConstant::getFitErrorMessage("THUMBNAIL_1920_560"));
            }

            $title         = $this->request->post('title');
            $isAlwaysShow  = $this->request->post('is_always_show');
            $showStartedAt = $this->request->post('show_started_at');
            $showEndedAt   = $this->request->post('show_ended_at');
            $isShow        = $this->request->post('is_show');

            $params = [
                'title'         => $title,
                'isAlwaysShow'  => $isAlwaysShow,
                'showStartedAt' => $showStartedAt,
                'showEndedAt'   => $showEndedAt,
                'isShow'        => $isShow,
                'thumbnail'     => $thumbnail,
            ];

            $result = $this->mainService->topBannerEdit($id, $params);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                throw new Exception($result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    function topBannerDelete(int $id): JsonResponse
    {
        try {
            $result = $this->mainService->topBannerDelete($id);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                throw new Exception($result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    function introList(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'page'      => 'required|int',
                'page_size' => 'required|string',
            ], [
                'page.required'      => MainErrorMessageConstant::getNotHaveErrorMessage("PAGE"),
                'page_size.required' => MainErrorMessageConstant::getNotHaveErrorMessage("PAGE_SIZE"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $page      = $this->request->get("page", 1);
            $pageSize  = $this->request->get("page_size", 50);
            $isShow    = $this->request->get("is_show", "") ?? "";
            $searchCls = $this->request->get("search_cls", "") ?? "";
            $keyword   = $this->request->get("keyword", "") ?? "";
            $sort      = $this->request->get("sort", "created_at|desc") ?? "created_at|desc";

            if( $pageSize > 50 ) $pageSize = 50;

            $params = [
                'page'      => $page,
                'pageSize'  => $pageSize,
                'isShow'    => $isShow,
                'searchCls' => $searchCls,
                'keyword'   => $keyword,
                'sort'      => $sort,
            ];

            $result = $this->mainService->introList($params);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                throw new Exception($result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    function introCreate(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'title'     => 'required|string',
                'is_show'   => 'required|string',
                'desc'      => 'required|string',
                'thumbnail' => 'required|file',
            ], [
                'title.required'     => MainErrorMessageConstant::getNotHaveErrorMessage("TITLE"),
                'is_show.required'   => MainErrorMessageConstant::getNotHaveErrorMessage("IS_SHOW"),
                'desc.required'      => MainErrorMessageConstant::getNotHaveErrorMessage("DESC"),
                'thumbnail.required' => MainErrorMessageConstant::getNotHaveErrorMessage("THUMBNAIL"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $thumbnail = $this->request->file('thumbnail');
            if (strpos($thumbnail->getClientMimeType(), 'image') !== 0) {
                throw new Exception(MainErrorMessageConstant::getFitErrorMessage("THUMBNAIL_TYPE"));
            }
            // 파일 크기 검증 (2MB 이하)
            if ($thumbnail->getSize() > 2 * 1024 * 1024) {
                throw new Exception(MainErrorMessageConstant::getFitErrorMessage("THUMBNAIL_SIZE_2MB"));
            }
            // 이미지 크기 검증 (413x271)
            list($width, $height) = getimagesize($thumbnail->getPathname());
            if ($width > 413 || $height > 271) {
                throw new Exception(MainErrorMessageConstant::getFitErrorMessage("THUMBNAIL_413_271"));
            }

            $title  = $this->request->post('title');
            $isShow = $this->request->post('is_show');
            $desc   = $this->request->post('desc');

            $params = [
                'title'     => $title,
                'isShow'    => $isShow,
                'desc'      => $desc,
                'thumbnail' => $thumbnail,
            ];

            $result = $this->mainService->introCreate($params);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                throw new Exception($result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    function introEdit(int $id): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'title'     => 'required|string',
                'is_show'   => 'required|string',
                'desc'      => 'required|string',
                'thumbnail' => 'required|file',
            ], [
                'title.required'     => MainErrorMessageConstant::getNotHaveErrorMessage("TITLE"),
                'is_show.required'   => MainErrorMessageConstant::getNotHaveErrorMessage("IS_SHOW"),
                'desc.required'      => MainErrorMessageConstant::getNotHaveErrorMessage("DESC"),
                'thumbnail.required' => MainErrorMessageConstant::getNotHaveErrorMessage("THUMBNAIL"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $thumbnail = $this->request->file('thumbnail');
            if (strpos($thumbnail->getClientMimeType(), 'image') !== 0) {
                throw new Exception(MainErrorMessageConstant::getFitErrorMessage("THUMBNAIL_TYPE"));
            }
            // 파일 크기 검증 (2MB 이하)
            if ($thumbnail->getSize() > 2 * 1024 * 1024) {
                throw new Exception(MainErrorMessageConstant::getFitErrorMessage("THUMBNAIL_SIZE_2MB"));
            }
            // 이미지 크기 검증 (413x271)
            list($width, $height) = getimagesize($thumbnail->getPathname());
            if ($width > 413 || $height > 271) {
                throw new Exception(MainErrorMessageConstant::getFitErrorMessage("THUMBNAIL_413_271"));
            }

            $title  = $this->request->post('title');
            $isShow = $this->request->post('is_show');
            $desc   = $this->request->post('desc');

            $params = [
                'title'     => $title,
                'isShow'    => $isShow,
                'desc'      => $desc,
                'thumbnail' => $thumbnail,
            ];

            $result = $this->mainService->introEdit($id, $params);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                throw new Exception($result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    function introDelete(int $id): JsonResponse
    {
        try {
            $result = $this->mainService->introDelete($id);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                throw new Exception($result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    function intro2List(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'page'      => 'required|int',
                'page_size' => 'required|string',
            ], [
                'page.required'      => MainErrorMessageConstant::getNotHaveErrorMessage("PAGE"),
                'page_size.required' => MainErrorMessageConstant::getNotHaveErrorMessage("PAGE_SIZE"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $page      = $this->request->get("page", 1);
            $pageSize  = $this->request->get("page_size", 50);
            $isShow    = $this->request->get("is_show", "") ?? "";
            $searchCls = $this->request->get("search_cls", "") ?? "";
            $keyword   = $this->request->get("keyword", "") ?? "";
            $sort      = $this->request->get("sort", "created_at|desc") ?? "created_at|desc";

            if( $pageSize > 50 ) $pageSize = 50;

            $params = [
                'page'      => $page,
                'pageSize'  => $pageSize,
                'isShow'    => $isShow,
                'searchCls' => $searchCls,
                'keyword'   => $keyword,
                'sort'      => $sort,
            ];

            $result = $this->mainService->intro2List($params);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                throw new Exception($result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    function intro2Create(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'title'     => 'required|string',
                'is_show'   => 'required|string',
                'thumbnail' => 'required|file',
            ], [
                'title.required'     => MainErrorMessageConstant::getNotHaveErrorMessage("TITLE"),
                'is_show.required'   => MainErrorMessageConstant::getNotHaveErrorMessage("IS_SHOW"),
                'thumbnail.required' => MainErrorMessageConstant::getNotHaveErrorMessage("THUMBNAIL"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $thumbnail = $this->request->file('thumbnail');
            if (strpos($thumbnail->getClientMimeType(), 'image') !== 0) {
                throw new Exception(MainErrorMessageConstant::getFitErrorMessage("THUMBNAIL_TYPE"));
            }
            // 파일 크기 검증 (2MB 이하)
            if ($thumbnail->getSize() > 2 * 1024 * 1024) {
                throw new Exception(MainErrorMessageConstant::getFitErrorMessage("THUMBNAIL_SIZE_2MB"));
            }
            // 이미지 크기 검증 (293x581)
            list($width, $height) = getimagesize($thumbnail->getPathname());
            if ($width > 293 || $height > 581) {
                throw new Exception(MainErrorMessageConstant::getFitErrorMessage("THUMBNAIL_293_581"));
            }

            $title  = $this->request->post('title');
            $isShow = $this->request->post('is_show');

            $params = [
                'title'     => $title,
                'isShow'    => $isShow,
                'thumbnail' => $thumbnail,
            ];

            $result = $this->mainService->intro2Create($params);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                throw new Exception($result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    function intro2Edit(int $id): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'title'     => 'required|string',
                'is_show'   => 'required|string',
                'thumbnail' => 'required|file',
            ], [
                'title.required'     => MainErrorMessageConstant::getNotHaveErrorMessage("TITLE"),
                'is_show.required'   => MainErrorMessageConstant::getNotHaveErrorMessage("IS_SHOW"),
                'thumbnail.required' => MainErrorMessageConstant::getNotHaveErrorMessage("THUMBNAIL"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $thumbnail = $this->request->file('thumbnail');
            if (strpos($thumbnail->getClientMimeType(), 'image') !== 0) {
                throw new Exception(MainErrorMessageConstant::getFitErrorMessage("THUMBNAIL_TYPE"));
            }
            // 파일 크기 검증 (2MB 이하)
            if ($thumbnail->getSize() > 2 * 1024 * 1024) {
                throw new Exception(MainErrorMessageConstant::getFitErrorMessage("THUMBNAIL_SIZE_2MB"));
            }
            // 이미지 크기 검증 (293x581)
            list($width, $height) = getimagesize($thumbnail->getPathname());
            if ($width > 293 || $height > 581) {
                throw new Exception(MainErrorMessageConstant::getFitErrorMessage("THUMBNAIL_293_581"));
            }

            $title  = $this->request->post('title');
            $isShow = $this->request->post('is_show');

            $params = [
                'title'     => $title,
                'isShow'    => $isShow,
                'thumbnail' => $thumbnail,
            ];

            $result = $this->mainService->intro2Edit($id, $params);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                throw new Exception($result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    function intro2Delete(int $id): JsonResponse
    {
        try {
            $result = $this->mainService->intro2Delete($id);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                throw new Exception($result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }
}
