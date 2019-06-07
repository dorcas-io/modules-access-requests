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
            'selectedMenu' => 'modules-access-requests',
            'submenuConfig' => 'navigation-menu.modules-access-requests.sub-menu',
            'submenuAction' => ''
        ];
    }

    public function index(Request  $request)
    {
    	$this->data['availableModules'] = HomeController::SETUP_UI_COMPONENTS;
        $this->setViewUiResponse($request);
    	return view('modules-access-requests::index', $this->data);
    }


    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'business_id' => 'required|string',
            'modules' => 'required|array',
            'modules.*' => 'required|string'
        ]);
        # validate the request
        try {
            $query = $sdk->createCompanyResource($request->input('business_id'));
            $data = $request->only(['modules']);
            foreach ($data as $key => $value) {
                $query->addBodyParam($key, $value);
            }
            $query = $query->send('post', ['access-grant-requests']);
            # send the request
            if (!$query->isSuccessful()) {
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while sending the request. '.$message);
            }
            $response = (tabler_ui_html_response(['Successfully sent the request.']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
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