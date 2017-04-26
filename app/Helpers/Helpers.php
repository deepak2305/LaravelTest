<?php

class Helpers {

    public static function dump($array) {

        echo '<pre>';
        print_r($array);
        echo '</pre>';
    }
    
    /*
    ********************************************************************************************************
                                           CRON 
    ********************************************************************************************************
    */
    
    # split process into multiple process
    public static function can_i_run($process) {
            $output = `ps aux | grep patcognos-"$process"`;
            $lines = explode( "\n", $output );
            foreach( $lines as $k => $line ) {
                    if( empty( $line ) ) {
                            unset( $lines[ $k ] );
                    }
            }
            //Helpers::dump($lines);

            // Current process is initiated. So, one more isntance need to be checked.
            return count( $lines ) >= 53 ? false : true; //Allow 50 instances
    }

    public static function can_cron_run($process) {
        $can_run = false;
        if (PHP_OS != 'WINNT') {
            if (\Helpers::can_i_run($process) == false) {
                $can_run = false;
                echo "Can't run yet, waiting for previous instance to finish.";
            } else {
                $can_run = true;
                echo "Good to run now \n";
            }
        } else {
            $can_run = true;
            echo "Windows Good to run now \n";
        }

        if ($can_run) {
            return true;
        } else {
            echo "Can't run yet, waiting for previous instance to finish.";
        }
    }


    public static function check_execution_time_eligibility($activity) {
        return true;
    }

    /*
    ********************************************************************************************************
                                           Pagination
    ********************************************************************************************************
    */
    public static function requestToParamsForBackEnd($numResults, $totalPages, $firstPage, $lastPage, $currentPage, $start, $sort, $order, $q, $field) {
        // function requestToParamsForBackEnd($numResults, $start, $totalPages, $firstPage, $lastPage, $currentPage, $sort, $order, $q, $field) {
        // $PER_PAGE = 2;
        $PER_PAGE = Config::get('app.per_page');
        $param = array(
            'numResults' => $numResults,
            'totalPages' => $totalPages,
            'firstPage' => $firstPage,
            'lastPage' => $lastPage,
            'currentPage' => $currentPage,
            'start' => $start,
            'end' => (($start + ($PER_PAGE - 1)) > ($numResults - 1) ? ($numResults - 1) : ($start + ($PER_PAGE - 1))),
            'perPage' => $PER_PAGE,
            'sort' => $sort,
            'order' => $order,
            'q' => $q,
            'field' => $field,
            'tab' => $field
        );
        return $param;
    }



    public static function buildPagination($param, $uri) {
        
        $html = '';
        
        
        if (!empty($param) && count($param) > 0) {
            $adjacents = 2;
            $page = $param ['currentPage'];
            // $page = $param['page'];

            $prev = $page - 1;
            $next = $page + 1;
            $lastpage = $param ['lastPage'];
            $lpm1 = $lastpage - 1;

            if ($lastpage > 1) {
                $html .= '<ul class="pagination">';

                // previous button
                if ($page > 0) {
                    $html .= '<li class="first"><a  href="' . $uri . '/page/' . $param ['firstPage'] . '" id = "page' . ((isset($param ['field']) && !empty($param ['field'])) ? '__' . $param ['field'] : '') . '__' . $param ['firstPage'] . '">First</a></li>';
                    $html .= '<li class="prev"><a  href="' . $uri . '/page/' . $page . '" id = "page' . ((isset($param ['field']) && !empty($param ['field'])) ? '__' . $param ['field'] : '') . '__' . $prev . '"> &laquo; </a></li>';
                } else {
                    $html .= '<span class="disabledFirst"> << </span>';
                }
                // pages
                if ($lastpage < 5 + ($adjacents * 2)) {
                    for ($counter = 1; $counter <= $lastpage; $counter ++) {
                        if ($counter == $page) {
                            $html .= '<li class="current"><a href="' . $uri . '/page/' . $counter . '"  id = "page' . ((isset($param ['field']) && !empty($param ['field'])) ? '__' . $param ['field'] : '') . '__' . $counter . '">' . $counter . '</a></li>';
                        } else {
                            $html .= '<li><a href="' . $uri . '/page/' . $counter . '" id = "page' . ((isset($param ['field']) && !empty($param ['field'])) ? '__' . $param ['field'] : '') . '__' . $counter . '">' . $counter . '</a></li>';
                        }
                    }
                } else if ($lastpage > 3 + ($adjacents * 2)) {
                    if ($page < 1 + ($adjacents * 2)) {
                        for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter ++) {
                            if ($counter == $page) {
                                $html .= '<li class="current"><a href="' . $uri . '/page/' . $counter . '" id = "page' . ((isset($param ['field']) && !empty($param ['field'])) ? '__' . $param ['field'] : '') . '__' . $counter . '">' . $counter . '</a></li>';
                            } else {
                                $html .= '<li><a href="' . $uri . '/page/' . $counter . '"  id = "page' . ((isset($param ['field']) && !empty($param ['field'])) ? '__' . $param ['field'] : '') . '__' . $counter . '">' . $counter . '</a></li>';
                            }
                        }

                        $html .= '<li class="unavailable"><a href="">&hellip;</a></li>';

                        // $html.= '<li>...</li>';
                        $html .= '<li><a href="' . $uri . '/page/' . $lpm1 . '"  id = "page' . ((isset($param ['field']) && !empty($param ['field'])) ? '__' . $param ['field'] : '') . '__' . $lpm1 . '">' . $lpm1 . '</a></li>';
                        $html .= '<li><a href="' . $uri . '/page/' . $lastpage . '"  id = "page' . ((isset($param ['field']) && !empty($param ['field'])) ? '__' . $param ['field'] : '') . '__' . $lastpage . '">' . $lastpage . '</a></li>';
                    } else if ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                        $html .= '<li><a href="' . $uri . '/page/' . $param ['firstPage'] . '"  id = "page' . ((isset($param ['field']) && !empty($param ['field'])) ? '__' . $param ['field'] : '') . '__' . $param ['firstPage'] . '">1</a></li>';
                        $html .= '<li><a href="' . $uri . '/page/' . $next . '"   id = "page' . ((isset($param ['field']) && !empty($param ['field'])) ? '__' . $param ['field'] : '') . '__' . $next . '">2</a></li>';
                        $html .= '<li>...</li>';

                        for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter ++) {
                            if ($counter == $page) {
                                $html .= '<li class="current"><a href="' . $uri . '/page/' . $counter . '"  id = "page' . ((isset($param ['field']) && !empty($param ['field'])) ? '__' . $param ['field'] : '') . '__' . $counter . '">' . $counter . '</a></li>';
                            } else {
                                $html .= '<li><a href="' . $uri . '/page/' . $counter . '"  id = "page' . ((isset($param ['field']) && !empty($param ['field'])) ? '__' . $param ['field'] : '') . '__' . $counter . '">' . $counter . '</a></li>';
                            }
                        }
                        // $html.= '<li>...</li>';
                        $html .= '<li class="unavailable"><a href="">&hellip;</a></li>';
                        $html .= '<li><a href="' . $uri . '/page/' . $lpm1 . '"  id = "page' . ((isset($param ['field']) && !empty($param ['field'])) ? '__' . $param ['field'] : '') . '__' . $lpm1 . '">' . $lpm1 . '</a></li>';
                        $html .= '<li><a href="' . $uri . '/page/' . $lastpage . '"  id = "page' . ((isset($param ['field']) && !empty($param ['field'])) ? '__' . $param ['field'] : '') . '__' . $lastpage . '">' . $lastpage . '</a></li>';
                   
                    } else {
                        
                        $html .= '<li><a href="' . $uri . '/page/' . $param ['firstPage'] . '"  id = "page' . ((isset($param ['field']) && !empty($param ['field'])) ? '__' . $param ['field'] : '') . '__' . $param ['firstPage'] . '">1</a></li>';
                        $html .= '<li><a href="' . $uri . '/page/' . $next . '"  id = "page' . ((isset($param ['field']) && !empty($param ['field'])) ? '__' . $param ['field'] : '') . '__' . $next . '">2</a></li>';
                        // $html.= '<li>...</li>';
                        $html .= '<li class="unavailable"><a href="">&hellip;</a></li>';

                        for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter ++) {
                            if ($counter == $page) {
                                $html .= '<li class="current"><a href="' . $uri . '/page/' . $counter . '"  id = "page' . ((isset($param ['field']) && !empty($param ['field'])) ? '__' . $param ['field'] : '') . '__' . $counter . '">' . ($counter) . '</a></li>';
                            } else {
                                $html .= '<li><a href="' . $uri . '/page/' . $counter . '"  id = "page' . ((isset($param ['field']) && !empty($param ['field'])) ? '__' . $param ['field'] : '') . '__' . $counter . '">' . $counter . '</a></li>';
                            }
                        }
                    }
                }
                // next button
                if ($page < $counter - 1) {
                    $html .= '<li class="next"><a  href="' . $uri . '/page/' . $next . '"  id = "page' . ((isset($param ['field']) && !empty($param ['field'])) ? '__' . $param ['field'] : '') . '__' . $next . '"> &raquo; </a></li>';
                } else {
                    $html .= '<li class="next disabled"><a > &raquo; </a></li>';
                }
                $html .= '<li class="last"><a   href="' . $uri . '/page/' . $param ['lastPage'] . '" id = "page' . ((isset($param ['field']) && !empty($param ['field'])) ? '__' . $param ['field'] : '') . '__' . $lastpage . '">Last</a></li>';
                $html .= '</ul>';

                return $html;
            }
        } else {
            return $html;
        }
    }

  
    
 


    public static function processItemInput($q) {

        $response = array();

        $arr_query = explode(' ', $q);

        if (!empty($q)) {

            // check if white espace is exist or not
            if(!preg_match('/\s/',($q))) {


                // check if it contain only integer
                if(preg_match("/^[0-9]+$/",$q)){

                    $response ['type'] = 'integer';
                    $response ['q'] = (int)$q;
                }

            }else if (count( $arr_query ) > 1) {

                $response ['type'] = 'string';
                $response ['q'] = $q;
            }
        } else {

            $response ['q'] = '';

        }


        return $response;
    }

    public static function buildLimitQuery($page,$limit) {

        $PER_PAGE = Config::get('app.per_page');

        $arr = array();

        $query = '';
        $start = 1;
        
        if (!empty($page) && $page == 'all') {
            // NO NEED TO LIMIT THE CONTENT
        } else {

            if (!empty($page) || $page != 0) {
                $start = ($page - 1) * $PER_PAGE;
                $query = " LIMIT $start, " . $PER_PAGE;
            } else {
                $query = " LIMIT 0, " . $PER_PAGE;
                $start = 1;
            }
        }

        $arr ['query'] = $query;
        $arr ['start'] = $start;

        return $arr;
    }

    public static function buildParamForPagination($numResults, $p, $per_page) {

        $arr = array();

        if (!empty($p) && $p == 'all') {
            $per_page = $numResults;
            $arr ['per_page'] = $per_page;
        }

        if ($numResults > 0) {
            $totalPages = ceil($numResults / $per_page);
        } else {
            $totalPages = 0;
        }

        $arr ['totalPages'] = $totalPages;
        $firstPage = 1;
        $arr ['firstPage'] = $firstPage;

        if ($totalPages > 0) {
            $lastPage = $totalPages;
        } else {
            $lastPage = 1;
        }
        $arr ['lastPage'] = $lastPage;

        $currentPage = '';
        if ($p) {
            $currentPage = $p;
        }

        if ($currentPage <= 0) {
            $currentPage = 1;
        } else if ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }
        $arr ['currentPage'] = $currentPage;

        return $arr;
    }

    public static function requestToParams($numResults, $start, $totalPages, $first, $last, $page, $sort, $order, $q, $field = null, $header = null ,  $target_tab = null,  $to_year=null ,  $from_year=null , $baseNumResults = null, $per_page = null) {
        
        //global $PER_PAGE;
        $PER_PAGE = $per_page;

        $param = array(
            'page' => $page,
            'totalPages' => $totalPages,
            'perPage' => $PER_PAGE,
            'start' => $start,
            'end' => (($start + ($PER_PAGE - 1)) > ($numResults - 1) ? ($numResults - 1) : ($start + ($PER_PAGE - 1))),
            'firstPage' => $first,
            'lastPage' => $last,
            'numResults' => $numResults,
            'sort' => $sort,
            'order' => $order,
            'q' => $q,
            'field' => $field,
            'result_header' => $header,
            'currentPage' => $page         
        );
        
        if($to_year && $from_year){
             $param ['to_year'] = $to_year;
             $param ['from_year'] = $from_year;
        }

        $tab = $target_tab;
        
        if ($tab) {
            $param ['tab'] = $tab;
        }

        if ($baseNumResults) {
            $param ['baseNumResults'] = $baseNumResults;
        }
        return $param;
    }
    
    /*
    ********************************************************************************************************
                                           Pagination
    ********************************************************************************************************
    */

    public static function buildErrorMessage($messages = array()) {
        $msg = null;
        $count = count($messages);
        $i  = 1;

        foreach ($messages as $message)
        {
            if( $count == 1 ){
                $msg .= $message ;
            }else{


                if(  $count == $i ){
                    $msg .= '-' . $message ;
                }else{
                    $msg .= '-' .$message . '<br>';
                    $i++;
                }


            }


        }
        return $msg;
    }



    public static function build_image($image, $categoryName, $size = '100') {

        if( !empty($image) ){
            return URL::to(sprintf('uploads/images/%s/%s/%s', $categoryName ,$size, $image));
        }else{
            return '';
        }


    }



    public static function build_delivery_image($size = '100') {
        return URL::to(sprintf('uploads/images/%s/%s/%s', 'delivery' ,$size, 'delivery.png'));
    }


    public static function build_taxi_image($size = '100') {
        return URL::to(sprintf('uploads/images/%s/%s/%s', 'taxi' ,$size, 'taxi.png'));
    }


    public static function build_movablewash_image($size = '100') {
        return URL::to(sprintf('uploads/images/%s/%s/%s', 'movablewash' ,$size, 'movablewash.png'));
    }

    public static function build_static_image($categoryName,$size = '100') {
        return URL::to(sprintf('uploads/images/%s/%s/%s', $categoryName ,$size, "{$categoryName}.png"));
    }


    public static function apiLimitQuery() {


        $page = \Input::get('page');
        if( empty($page) ){
            $page = 1;
        }

        $limit = \Input::get('limit');
        if( empty($limit) ){
            $limit = \Config::get('constant.api_limit');
        }

        $arr = array();

        $query = '';
        $start = 1;

        if (!empty($page) && $page == 'all') {
            // NO NEED TO LIMIT THE CONTENT
        } else {

            if (!empty($page) || $page != 0) {
                $start = ($page - 1) * $limit;
                $query = " LIMIT $start, " . $limit;
            } else {
                $query = " LIMIT 0, " . $limit;
                $start = 1;
            }
        }

        $arr ['query'] = $query;
        $arr ['start'] = $start;

        return $arr;
    }


    public static function getKPICalendarStatuses() {

        return array('1'=>'new',  '2'=>'approved',  '3'=>'rescheduled', '4'=>'reassigned', '5'=>'released', '6'=>'delayed');

    }

    public static function getKPICalendarStatusesForDropDown() {


        //return array('5'=>'Complete', '2'=>'Pending', '6'=>'Delay' ,  '3'=>'Scheduled FTD', '4'=>'Re-assigned', '1'=>'new' );

        return array('Scheduled', 'Released', 'Delayed', 'Pending' );
    }

    public static function getKPICalendarStatusCodes(){

        $busServices = SELF::getKPICalendarStatuses();
        return  array_flip($busServices);

    }



    public static function buildStatus($status) {

        $str = null;
        if( !empty($status) ){

            if( $status == 5 ){
                $str = '<span class="font-size-12 label label-success col-sm-12">Complete</span>';
            }elseif( $status == 2 ){
                $str = '<span class=" font-size-12 label label-danger col-sm-12">Pending</span>';
            }elseif( $status == 6 ){
                $str = '<span class="font-size-12 label label-warning col-sm-12">Delay</span>';
            }elseif( $status == 3 ){
                $str = '<span class="font-size-12 label label-default col-sm-12">Scheduled FTD</span>';
            }elseif( $status == 4 ){
                $str = '<span class="font-size-12 label label-info col-sm-12">Re assigned</span>';
            }elseif( $status == 1 ){
                $str = '<span class="font-size-12 label bg-blue col-sm-12">New</span>';
            }


        }

        return $str;
    }


    public static function getKPICalendarStatus($status) {
        $getStatusCodes = SELF::getKPICalendarStatusCodes();
        return !empty($getStatusCodes[$status]) ? $getStatusCodes[$status] : '1';
    }

    public static function getKPICalendarStatusNotFreezedYet($status) {

        $statuses = array('Scheduled', 'Released', 'Delayed', 'Pending' );
        $getStatusCodes = array_flip($statuses);
        return !empty($getStatusCodes[$status]) ? $getStatusCodes[$status] : '1';
    }


    public static function getDelayRanges() {


        $step = Config::get('constants.delay_step');
        //$step = 5;
        $lowerRange = range(0,25,$step);
        $upperRange = range(5,30,$step);

        $data = array();
        for($i=0; $i<count($lowerRange); $i++ ){
            $data["{$lowerRange[$i]}-{$upperRange[$i]}"] = " > {$lowerRange[$i]}&nbsp;&nbsp;&nbsp;&nbsp;to&nbsp;&nbsp;&nbsp;&nbsp;<= {$upperRange[$i]} days";
        }

        return $data;

    }


    public static function getCompanyForDropDown() {
        return \Modules\BPM\Entities\Company::get(array('company_name', 'company_id'));
    }

    public static function getRegionForDropDown() {
        return \Modules\BPM\Entities\Region::get(array('name', 'region_id'));
    }

    public static function getHubForDropDown() {
        return \Modules\BPM\Entities\Hub::get(['hub_name', 'hub_id']);
    }

    public static function getVerticalForDropDown() {
        return \Modules\BPM\Entities\Vertical::get(array('vertical_name', 'vertical_id'));
    }

    public static function getGroupActivityHierarchyFilterData($select = null) {

        $datas = \Modules\BPM\Entities\Groupactivity::get(array('name', 'group_activity_id', 'parent'))->toArray();

        return SELF::buildDDForGroupActivity(SELF::buildTree($datas), 0, null, $select);

    }

    public static function getGroupActivityExceptionHierarchyFilterData($group_activity_id, $select = null) {

        $datas = \Modules\BPM\Entities\GroupactivityExceptions::where('group_activity_id','=',$group_activity_id)->get(array('name', 'group_activity_exception_id', 'parent'))->toArray();

        return SELF::buildDDForGroupActivityException(SELF::buildExceptionTree($datas), 0, null, $select);

    }

    public static function getCompanyFilterData(){
        $data = array();
        $data = \Modules\BPM\Entities\Company::get(array('company_name', 'company_id'))->toArray();
        return $data;
    }

    public static function getCircalFilterData(){
        $data = array();
        $data = \Modules\BPM\Entities\Circle::get(array('circle_name', 'circle_id'))->toArray();
        return $data;
    }

    public static function getVerticaFilterData(){
        $data = array();
        $data = \Modules\BPM\Entities\Vertical::get(array('vertical_name', 'vertical_id'))->toArray();
        return $data;
    }

    public static function getGroupActivityFilterData(){

        return \Modules\BPM\Entities\Groupactivity::get(array('name', 'group_activity_id'))->toArray();

    }

    public static function getPerPageData(){
        $perpage = range(25,100,25);
        $perpage = range(5,100,5);
        return $perpage;
    }


    public static function getRLData(){
        $numbers = range(10,100);

        $repeatedNumber = $numbers[array_rand($numbers)];

        $countEx = range(10,100);
        $countExAdd = $countEx[array_rand($countEx)];

        $count_of_exception = $repeatedNumber + $countExAdd;

        return array(
            'repeated_number' => $repeatedNumber,
            'count_of_exception' => $count_of_exception,
            'repeat_percent' => ceil(($repeatedNumber/$count_of_exception)*100),
            'total_rl' => ($count_of_exception-$repeatedNumber)*100,
        );
    }

    public static function getFrequencyFilter() {

        return array('weekly'=>'Weekly',  'fortnightly'=>'Fortnightly',  'monthly'=>'Monthly');

    }

    public static function getWeekDaysForFilter() {

        return (new \Modules\BPM\Entities\Weekday())->getWeekdays();

    }

    public static function getFrequencyValueFilter($frequency) {

        $html = '';
        if( !empty($frequency) ){

            if( $frequency == 'weekly' ){
                $html = '';
            }else if( $frequency == 'fortnightly' ){

            }else if( $frequency == 'monthly' ){

            }

        }
        return $html;

    }


    public static function buildExceptionTree(array $data, $parent = '') {

        $tree = array();
        foreach ($data as $d) {
            if ($d['parent'] == $parent) {
                $children = SELF::buildExceptionTree($data, $d['group_activity_exception_id']);
                // set a trivial key
                if (!empty($children)) {
                    $d['_children'] = $children;
                }
                $tree[] = $d;
            }
        }

        return $tree;

    }


    public static function buildTree(array $data, $parent = '') {

        $tree = array();
        foreach ($data as $d) {
            if ($d['parent'] == $parent) {
                $children = SELF::buildTree($data, $d['group_activity_id']);
                // set a trivial key
                if (!empty($children)) {
                    $d['_children'] = $children;
                }
                $tree[] = $d;
            }
        }
        return $tree;

    }


    public static function getDataForGroupActivityTree($select = null) {

        $datas = \Modules\BPM\Entities\Groupactivity::get(array('name as text', 'group_activity_id as id', 'parent'))->toArray();

        return json_encode(SELF::buildDataForGroupActivityTree($datas, $parent = ''));

    }




    public static function buildDataForGroupActivityTree(array $data, $parent = '') {

        $tree = array();
        foreach ($data as $d) {
            if ($d['parent'] == $parent) {
                $children = SELF::buildDataForGroupActivityTree($data, $d['id']);
                // set a trivial key
                if (!empty($children)) {
                    $d['nodes'] = $children;
                }
                $tree[] = $d;
            }
        }
        return $tree;

    }

    public static function buildDataForGroupActivityListTree(array $data, $parent = '') {

        $tree = array();
        foreach ($data as $d) {
            if ($d['parent'] == $parent) {
                $children = SELF::buildDataForGroupActivityListTree($data, $d['group_activity_id']);
                // set a trivial key
                if (!empty($children)) {
                    $d['nodes'] = $children;
                }
                $tree[] = $d;
            }
        }
        return $tree;

    }



    public static function buildDDForGroupActivity($tree, $r = 0, $p = null, $select = null) {
        foreach ($tree as $i => $t) {
            $dash = ($t['parent'] == 0) ? '' : str_repeat('-', $r) . ' ';

            if ( $select == $t['group_activity_id']) {
                printf("\t<option value='%d' selected='selected'>%s%s</option>\n", $t['group_activity_id'], $dash, $t['name']);

            }else{
                printf("\t<option value='%d'>%s%s</option>\n", $t['group_activity_id'], $dash, $t['name']);

            }


            if ($t['parent'] == $p) {
                // reset $r
                $r = 0;
            }
            if (isset($t['_children'])) {
                SELF::buildDDForGroupActivity($t['_children'], ++$r, $t['parent'], $select);
            }
        }
    }

    public static function buildDDForGroupActivityException($tree, $r = 0, $p = null, $select = null) {
        foreach ($tree as $i => $t) {
            $dash = ($t['parent'] == 0) ? '' : str_repeat('-', $r) . ' ';

            if ( $select == $t['group_activity_exception_id']) {
                printf("\t<option value='%d' selected='selected'>%s%s</option>\n", $t['group_activity_exception_id'], $dash, $t['name']);

            }else{
                printf("\t<option value='%d'>%s%s</option>\n", $t['group_activity_exception_id'], $dash, $t['name']);

            }


            if ($t['parent'] == $p) {
                // reset $r
                $r = 0;
            }
            if (isset($t['_children'])) {
                SELF::buildDDForGroupActivityException($t['_children'], ++$r, $t['parent'], $select);
            }
        }
    }


    public static function getScheduleDates($day_of_month, $freqency , $month = 'january') {

//        try {
//            //this|next|previous
//            $date = new DateTime("first day of {$month} month");
//        } catch (Exception $e) {
//            return false;
//            //echo $e->getMessage();
//        }




        if( !empty($day_of_month) && !empty($freqency) ){


            //Tested for every month
            if( $freqency == 'weekly' ){
                //$day_of_month = 'wednesday';
                //$month = 'march';

                try {

                    //Note:: This is intentional to achieve perfect rang of dates, - Subtracted one day from first day,
                    // and added one day in last day

                    //$day_of_month = strtolower($day_of_month);
                    $weekelyDays = new DatePeriod(
                        (new DateTime("first day of {$month}"))->modify("-1 day")->modify("first {$day_of_month}"),
                        DateInterval::createFromDateString("next {$day_of_month}"),
                        (new DateTime("last day of {$month}"))->modify("+1 day")
                    );

                } catch (Exception $e) {
//                    echo $e->getMessage();
//                    die;
                    return false;
                }

                $dateArr = [];
                foreach ($weekelyDays as $weekelyDay) {
                    $dateArr[] = $weekelyDay->format("Y-m-d");
                }

                return $dateArr;


            }else if( $freqency == 'monthly' || $freqency == 'fortnightly' ){

                $daysArr = explode(',',$day_of_month);

                if( !empty($daysArr[0]) && count($daysArr) >= 1  ){

                    $dateArr = [];
                    foreach($daysArr as $key=>$val ){

                        if( !empty($val) ){

                            try {
                                $val = $val-1;
                                $dateArr[] = (new DateTime("first day of {$month}"))->modify("+{$val} day")->format('Y-m-d');
                            } catch (Exception $e) {
                                continue;
                            }
                        }

                    }

                    return $dateArr;


                }else{
                    return false;
                }

            }else{
                return false;
            }

        }else{
            return false;
        }

    }



    public static function getMonthsToBuildsKpiCalendar() {
        return array(
            'this'=>'This Month',
            'next'=>'Next Month',
            'previous'=>'Previous Month',
        );
    }

    public static function buildActivityCode($param) {
        return implode('-',array_filter($param));
    }

    public static function getActivityParentChildCode($activity) {
        return implode('-',array_filter($activity));
    }


    public static function getErrors($activities){
        $errors = [];
        $isValid = true;
        foreach($activities as $group_activity_id => $activity){

            //$errors["group_activity_block__{$group_activity_id}"] = [];
            $data = [];
            if( empty($activity['employee']) || is_int($activity['employee']) ){
                $isValid = false;
                $data[] = 'Employee field is required';

            }

            if( empty($activity['frequency']) ){

                $isValid = false;
                $data[] = 'Frequency field is required';

            }else{

                if( empty($activity[$activity['frequency']]) ){
                    $isValid = false;
                    $data[] = "{$activity['frequency']} field is required";

                }else{

                    $frequency = $activity['frequency'];
                    $frequency_data = $activity[$activity['frequency']];

                    if( $frequency == 'weekly' ){

                        $weekdaysArr = ['sunday','saturday','friday','thursday','wednesday','tuesday','monday'];
                        if( !in_array($frequency_data,$weekdaysArr) ){
                            $isValid = false;
                            $data[] = "{$frequency} field should be valid";
                        }

                    }else if( $frequency == 'fortnightly' || $frequency == 'monthly' ){

                        if ( !preg_match('/^\d+(?:,\d+)*$/', $frequency_data) ){
                            $isValid = false;
                            $data[] = "{$frequency} field should be number with comma seprated";

                        }else{
                            //check number should be between 1 to 31
                            if ( !SELF::isValidDigits($frequency_data) ){
                                $isValid = false;
                                $data[] = "{$frequency} should be between 1 to 31";
                            }
                        }

                    }

                }

            }


            if( !empty($data) ){
                $errors["group_activity_block__{$group_activity_id}"] = $data;
            }

        }

        return array('status' => $isValid, 'errors'=>$errors);

    }

    public static function isValidDigits($frequency_data){
        $frequencydays = explode(',',$frequency_data);
        $isValid = true;
        foreach($frequencydays as $d ){

            if ( !preg_match('/^([1-9]|[12]\d|3[0-1])$/', $d) ){
                $isValid = false;
                break;
            }
        }
        return $isValid;
    }

    public static function getImportAbsolutePath(){
        return public_path() . '/import/';
    }



    public static function buildMscVsInData(array $data, $parent = '') {

        $tree = array();
        foreach ($data as $d) {
            if ( is_numeric($d['sno']) ) {
            }else{
                $children = SELF::buildMscVsInData($data, $d['sno']);
                // set a trivial key
                if (!empty($children)) {
                    $d['childrens'] = $children;
                }
            }
            $tree[] = $d;
        }

        return $tree;

    }


    public static function isDecimal( $val )
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }


    public static function getNumberOfWeekByDate($date) {

        //For our logic to get number of week
//        $dayWeek = (new DateTime($date))
//            ->format('l');
        $dayNum = (new DateTime($date))
            ->format('d');

        return floor(($dayNum - 1) / 7) + 1;


        //For PHP - to get number of Week
//        $dayWeek = (new DateTime($date))
//            ->format('W');
//
//        $firstdayWeek = (new DateTime($date))
//            ->modify('first day of this month')
//            ->format('W');
//        return ( $dayWeek - $firstdayWeek ) + 1;



        /*$ddate = "2017-02-27";
        $firstday = new DateTime($ddate);
        $lastday = new DateTime($ddate);
        $firstdayWeek = $firstday->modify('first day of this month');
        $lastdayWeek = $lastday->modify('last day of this month');

        echo $days = $lastdayWeek->diff($firstdayWeek)->days*/


    }

    public static function getNumberOfFortnightlyByDate($date) {

        $dayWeek = (new DateTime($date))
            ->format('d');

        if( !empty($dayWeek) ){

            if( $dayWeek >= 1 && $dayWeek < 15 ){
                return '1';
            }elseif( $dayWeek >= 15 && $dayWeek < 32 ){
                return '2';
            }

        }else{
            return '2';
        }
    }

    public static function getBillCycleRowByDate($date) {

        $dayWeek = (new DateTime($date))
            ->format('d');

        if( !empty($dayWeek) ){

            if( $dayWeek >= 1 && $dayWeek <= 3 ){
                //First Bill cycle
                return '8';
            }elseif( $dayWeek > 3 && $dayWeek <= 6 ){
                //Second Bill cycle
                return '9';

            }elseif( $dayWeek > 6 && $dayWeek <= 9 ){
                //Third Bill cycle
                return '10';

            }elseif( $dayWeek > 9 && $dayWeek <= 12 ){
                //Fourth Bill cycle
                return '11';

            }elseif( $dayWeek > 12 && $dayWeek <= 15 ){
                //Fifth Bill cycle
                return '12';

            }elseif( $dayWeek > 15 && $dayWeek <= 18 ){
                //Sixth Bill cycle
                return '13';

            }elseif( $dayWeek > 18 && $dayWeek <= 21 ){
                //Seventh Bill cycle
                return '14';

            }elseif( $dayWeek > 21 && $dayWeek <= 24 ){
                //Eight Bill cycle
                return '15';

            }else{
                //First Bill cycle
                return '15';

            }

        }else{
            return '15';
        }
    }


    public static function getBillCycleNumberByDate($date) {

        $dayWeek = (new DateTime($date))
            ->format('d');

        if( !empty($dayWeek) ){

            if( $dayWeek >= 1 && $dayWeek <= 3 ){
                //First Bill cycle
                return '1';
            }elseif( $dayWeek > 3 && $dayWeek <= 6 ){
                //Second Bill cycle
                return '2';

            }elseif( $dayWeek > 6 && $dayWeek <= 9 ){
                //Third Bill cycle
                return '3';

            }elseif( $dayWeek > 9 && $dayWeek <= 12 ){
                //Fourth Bill cycle
                return '4';

            }elseif( $dayWeek > 12 && $dayWeek <= 15 ){
                //Fifth Bill cycle
                return '5';

            }elseif( $dayWeek > 15 && $dayWeek <= 18 ){
                //Sixth Bill cycle
                return '6';

            }elseif( $dayWeek > 18 && $dayWeek <= 21 ){
                //Seventh Bill cycle
                return '7';

            }elseif( $dayWeek > 21 && $dayWeek <= 24 ){
                //Eight Bill cycle
                return '8';

            }else{
                //First Bill cycle
                return '8';

            }

        }else{
            return '8';
        }
    }

    public static function getCurrentARPU( )
    {
        return 100;
    }


    public static function showDateTime($datetime)
    {
        return (new \DateTime($datetime))->format('Y-m-d H:i:s');
    }

    public static function showDate($date)
    {
        return (new \DateTime($date))->format('Y-m-d');
    }

    public static function showFullNameOfMonth($date)
    {
        return (new DateTime($date))->format('F');
    }

    public static function moneyformat($number){
        return number_format($number);

/*    // english notation (default)
            $english_format_number = number_format($number);
    // 1,235

    // French notation
            $nombre_format_francais = number_format($number, 2, ',', ' ');
    // 1 234,56

    // english notation without thousands separator
            $english_format_number = number_format($number, 2, '.', '');
    // 1234.57*/

    }

    public static function getStartAndEndDatesByMonth($month){
        return [
            'start' => (new DateTime("first day of {$month} month"))->format('Y-m-d'),
            'end' => (new DateTime("last day of {$month} month"))->format('Y-m-d'),
        ];
    }


    public static function getAllMonthsName(){
        return [ 'january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december' ];
    }

    public static function getMonthsNameForCronScreenOfPublishKPICalendar(){
        return [ 'january', 'february', 'march' /*, 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'*/ ];
    }

    public static function getDataLevel(){
        $datalevel = Config::get('bpm::config.datalevel');
        if( empty($datalevel) || !is_array($datalevel) ){
            $datalevel = ['summary', 'exception'];
        }
        return $datalevel;

    }

    public static function getImportFileFormat(){
        $datalevel = Config::get('bpm::config.fileformat');
        if( empty($datalevel) || !is_array($datalevel) ){
            $datalevel = ['csv', 'excel', 'csv_excel'];
        }
        return $datalevel;

    }

    public static function getImportFileNameTag(){

        return ['opco','region','vertical','date','week','circle','srno'];//,'activity'

    }

    public static function getImportFileNameTagJson(){

        $data = SELF::getImportFileNameTag();
        $val = [];
        foreach($data as $k=>$v){
            $val[]= [ 'value'=>(++$k) ,'text'=>strtoupper($v) ];
        }

        return json_encode($val);
    }

}