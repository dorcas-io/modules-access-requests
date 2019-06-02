<?php

namespace Dorcas\ModulesAccessRequests\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dorcas\ModulesAccessRequests\Models\ModulesAccessRequests;
use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use App\Http\Controllers\HomeController;
use Hostville\Dorcas\Sdk;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ModulesAccessRequestsController extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->data = [
            'page' => ['title' => config('modules-access-requests.title')],
            'header' => ['title' => config('modules-access-requests.title')],
            'selectedMenu' => 'access-requests'
        ];
    }

    public function index()
    {
    	$this->data['availableModules'] = HomeController::SETUP_UI_COMPONENTS;
    	return view('modules-access-requests::index', $this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchByUser(Request $request, Sdk $sdk)
    {
        $search = $request->query('search');
        $sort = $request->query('sort', '');
        $order = $request->query('order', 'asc');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);
        # get the request parameters
        $resource = $sdk->createProfileService();
        $resource = $resource->addQueryArgument('limit', $limit)
                                ->addQueryArgument('page', get_page_number($offset, $limit));
        if (!empty($search)) {
            $resource->addQueryArgument('search', $search);
        }
        if ($request->has('statuses')) {
            $resource->addQueryArgument('statuses', $request->input('statuses'));
        }
        $response = $resource->send('get', ['access-requests']);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find any matching requests.');
        }
        $json = json_decode($response->getRawResponse(), true);
        return response()->json($json);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteRequestForUser(Request $request, Sdk $sdk, string $id)
    {
        $resource = $sdk->createProfileService();
        $response = $resource->send('delete', ['access-requests/' . $id]);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find delete the requests.');
        }
        $this->data = $response->getData();
        return response()->json($this->data);
    }


}