@extends('adminlte::layouts.app')

<?php
/*
?>
@section('contentheader_title')
    {{ trans('common.company') }} Configuration
@endsection

@section('contentheader_description')
    Add, Edit, Update and Delete List
@endsection
<?php
*/
?>

@section('main-content')

    <div class="row padding-top">

        <div class="col-md-7">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">List Of Tags</h3>
                </div>


                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <input id="filter__tag_list" name="filter__tag_list" class="form-control" type="text" placeholder="Search by Tag">

                        </div>

                    </div>
                    <div class="padding-top">
                        <div id = "list__tag_list"></div>
                    </div>
                </div>
            </div>
        </div>


        <?php
            /*
        ?>
        <div class="col-md-5">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">@if ( $company ) Update @else Add @endif Company</h3>

                </div>


                <form class="form-horizontal" method="POST" action="@if ( $company ){{route('company.update.save',$company->company_id)}}@else{{route('company.add.save')}}@endif">

                    @if ( $company )
                        {{ method_field('PATCH') }}
                    @endif


                    {{ csrf_field() }}

                    <div class="box-body">
                        <!-- Employee List : Drop down -->

                        @include('bpm::partials.alert')

                        <div class="form-group">
                            <label for="company_name" class="col-sm-4 control-label">Company Name</label>

                            <div class="col-sm-7">
                                <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Enter Company Name" value="@if ( $company ){!! $company->company_name !!}@else{!! old('company_name') !!}@endif">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="company_code" class="col-sm-4 control-label">Company Code</label>

                            <div class="col-sm-7">
                                <input type="text" class="form-control" id="company_code" name="company_code" placeholder="Enter Company Code" value="@if ( $company ){!! $company->company_code !!}@else{!! old('company_code') !!}@endif">
                            </div>
                        </div>


                        <!-- /.box-body -->
                        <div class="box-footer">


                            @if($company)
                                <a class="btn btn-primary btn-flat" href="{{ route('company.list') }}"><i class="fa fa-plus"></i> Add Company</a>
                            @else
                                <button type="reset" class="btn btn-flat btn-danger">Reset</button>
                            @endif


                            <button type="submit" class="btn btn-flat btn-success pull-right">@if ( $company ){{'Update'}}@else{{'Add'}}@endif Company</button>

                        </div>
                        <!-- /.box-footer -->
                    </div>
                </form>
            </div>
        </div>

        <?php
            */
        ?>

    </div>


    <div class="modal" id="confirm">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Delete Confirmation</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure you, want to delete?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-flat pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger btn-flat" id="delete-btn">Delete</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

@stop

@section('page-scripts')


    <script src="{{ asset('/js/jquery.typing-0.2.0.min.js') }}" type="text/javascript"></script>

    <script type="application/javascript">


        var field = "tag_list";
        var section = "tag";
        var attorney_listId = '';

        $(document).ready(function() {

            //alert($("meta[name='csrf-token']").attr("content"));

            getFilesList();



            $('#list__tag_list').on('click', '.form-delete', function(e){
                e.preventDefault();
                var $form=$(this);
                $('#confirm').modal({ backdrop: 'static', keyboard: false })
                    .on('click', '#delete-btn', function(){
                        $form.submit();
                    });
            });

            $("#filter__category").change(function() {
                getFilesList();
            });

            $("#filter__status").change(function() {
                getFilesList();
            });


            $("#filter__company").typing({
                start: function (event, $elem) {
                    //$elem.css('background', '#fa0');
                },
                stop: function (event, $elem) {
                    getFilesList();
                },
                delay: 1000
            });


            $("#filter__daterange").change(function() {
                getFilesList();
            });

        });


        function getFilesList() {

            record_id = "<?php //echo Request::segment(2); ?>";

            var category = $("#filter__category").val();
            var status = $("#filter__status").val();

            var field = "tag_list";

            listSpecificParams = {
                section: section,
                category: category,
                status: status,
                _token: $("meta[name='csrf-token']").attr("content")
            }

            if (window.location.hash) {
                str = window.location.hash;
                str = str.substr(2);
                arr = str.split('&');
                postArray = {};
                var extraParam = {};
                var page = q = '';
                for (i = 0; i < arr.length; i++) {
                    queryString = arr[i];
                    arr2 = queryString.split('=');
                    var key = '';
                    var value = '';
                    if (arr2[0]) {
                        key = arr2[0];
                    }
                    if (arr2[1]) {
                        value = arr2[0];

                    }

                    if (arr2[0] == 'page') {
                        page = arr2[1];
                    } else if (arr2[0] == 'q') {
                        q = arr2[1];
                    } else if (arr2[0] == 'sort') {
                        extraParam[arr2[0]] = arr2[1];
                    } else if (arr2[0] == 'order') {
                        extraParam[arr2[0]] = arr2[1];
                    }
                }

                if (q != '') {
                    filter = "filter__" + field;
                    filterObjString = "#" + filter;

                    $(filterObjString).val(q);
                }

                postArray = {
                    page: page,
                    q: q,
                    _token: $("meta[name='csrf-token']").attr("content"),
                }

                $.extend(postArray, extraParam);
                $.extend(postArray, listSpecificParams);
                buildDynamicList(field, record_id, postArray);
                reinitializeFilterBox(field);
            } else {
                postArray = {
                    section: section,
                    _token: $("meta[name='csrf-token']").attr("content")
                }

                $.extend(postArray, listSpecificParams);
                buildDynamicList(field, record_id, postArray);
                reinitializeFilterBox(field);
            }
        }


    </script>
@stop

