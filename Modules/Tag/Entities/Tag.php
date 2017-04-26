<?php

namespace Modules\Tag\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Helpers;

class Tag extends Model
{
    protected $fillable = ["tag","tag_code","created_by","updated_by","status"];

    protected $table = "tag";
    protected $guarded = ["tag_id"];
    protected $primaryKey = 'tag_id';


    //public function getList(Request $request){
    public function getList(){

        //$sequence_ID = 1;
        $PER_PAGE = 20;



        $q = \Request::input('q');
        $p = 1;

        if (isset($page) && $page > 0) {
            $p = $page; // Page
        } else {
            $p = \Request::input('page');
            if (!$p) {
                $p = \Request::input('page');
            }
        }


        $sort = $order = $where = $pp = '';
        $start = 1;

        $base_query = $query_count = '';

        $base_select_query = ' SELECT '.$this->table.'.* FROM '.$this->table.' ' ;

        $base_count_query = ' SELECT count('.$this->table.'.'.$this->primaryKey.' ) as num_records FROM '.$this->table.' ';

        $where = ' WHERE 1 ';


        $q = trim($q);
        if( !empty($q) ){
            if( is_numeric($q)){
                //$where .=  " AND ( phone LIKE '%".$q."%'  )";
            }else{
                $where .=  " AND ( {$this->table}.tag LIKE '%".$q."%' )";
            }
        }


        $base_query = $base_count_query . $where;
        $response = DB::select($base_query);
        $numResults = $response[0]->num_records;


        if ($numResults > 0) {
            $totalPages = ceil($numResults / $PER_PAGE);
            $firstPage = 1;

            if ($totalPages > 0) {
                $lastPage = $totalPages;
            } else {
                $lastPage = 1;
            }
        }

        if (empty($sort)) {
            $sort_query = ' ORDER BY '. $this->primaryKey . ' ' ;
            $sort = ' name  ';
        } else {
            $sort_query = ' ORDER BY ' . $sort;
        }

        if (empty($order)) {
            $order = 'DESC';
        }

        $query = $base_select_query . $where .$sort_query . $order;
        //echo $query;


        if (!empty($p) && $p == 'all') {
            // NO NEED TO LIMIT THE CONTENT
        } else {

            if (!empty($p) || $p != 0) {

                $start = ($p - 1) * $PER_PAGE;
                $query .= " LIMIT $start, " . $PER_PAGE;
            } else {
                $query .= " LIMIT 0, " . $PER_PAGE;
                $start = 0;
            }
        }



        //echo $query;die;
        $result = DB::select(($query));

        $results = array();
        foreach ($result as $row) {
            $results[] = $row;
        }

        //\Helpers::dump($results);
        //die;

        $paginationParams = \Helpers::buildParamForPagination($numResults, $p, $PER_PAGE);

        $field = "bpm_list";

        $params = \Helpers::requestToParams($numResults, $start, $paginationParams ['totalPages'], $paginationParams ['firstPage'], $paginationParams ['lastPage'], $paginationParams ['currentPage'], $sort, $order, $q, $field);
        $arr = array(
            'results' => $results,
            'param' => $params,
            //'query' => $params,
        );
        // \Helpers::dump($arr);die;
        return $arr;
    }


    function getCompanyById($company_id){
        return DB::table('company')
            ->select('company.*')
            ->where('company_id', '=', $company_id)
            ->where('status', '=', '1')
            ->first();

    }



}
