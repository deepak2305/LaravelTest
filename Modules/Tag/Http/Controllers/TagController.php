<?php

namespace Modules\Tag\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Modules\Tag\Entities\Tag;


class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {

        $tag = new Tag();
        $tag_lists = $tag->getList();

        // \Helpers::dump($tag_lists);die;

        return view('tag::index', array('TAGS' => $tag_lists));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('tag::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('tag::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('tag::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }


    public function postBuild(Request $request) {


        $formType   = $request->input('type');
        $record_id  = $request->input('record_id');
        $section    = $request->input('section');

        $data = array();

        $data = $this->getData($request);

        $html = $uri = $pagination = '';

        if (!empty($data)) {
            $html               = $this->getLoadView($formType, $data['results'], $section);
            $pagination         = \Helpers::buildPagination($data['param'], $uri);
        }

        if ($html == '') {
            $html = 'No added content';
        }

        $array = array(
            'html' => $html,
            'param' => (!empty($data) ? $data['param'] : '' ),
            'pagination' => $pagination
        );

        return response()->json($array);
    }

    /**
    Render Html View with database data
    ---------------------------------------------------------------------------------*/
    public function getLoadView($formType, $data, $section = NULL) {
        //$html = View::make('lists', $data)->render();
        if( !empty($section) ){
            $html = view("bpm::partials.dynamic_list.{$section}_list", $data)->render();
        }else{
            $html = view('bpm::partials.compliance_monitoring.lists', $data)->render();
        }

        //$response   = Response::make($html);
        return $html;
    }

    /**
    Fetch data from database - Used in List Build
    ---------------------------------------------------------------------------------*/
    public function getData($request) {

        $data  = array();

        $section    = $request->input('section');
        if( !empty($section) ){

            switch ($section) {
                case "revenue_leakage":
                    $items = new Tag();
                    $list  = $items->getList($request);

                    $arr   = array('KPIS' => $list );
                    $data['results'] = $arr;
                    $data['param']   = $list['param'];

                    break;

                default: // compliance monitering
                    $items = new Kpicalendar();
                    $list  = $items->getList($request);

                    $arr   = array('KPIS' => $list );
                    $data['results'] = $arr;
                    $data['param']   = $list['param'];
            }

        }else{ // compliance monitering

            $items = new Kpicalendar();
            $list  = $items->getList($request);

            $arr   = array('KPIS' => $list );
            $data['results'] = $arr;
            $data['param']   = $list['param'];

        }


        return $data;
    }
}
