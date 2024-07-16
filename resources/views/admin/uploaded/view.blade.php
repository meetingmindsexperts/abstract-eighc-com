@extends('master.admin-master')

@section('title','View Abstracts')

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ url('admin/plugins/table/datatable/datatables.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('admin/plugins/table/datatable/custom_dt_html5.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('admin/plugins/table/datatable/dt-global_style.css') }}">
@endpush

@section('content')

<div id="flStackForm" class="col-lg-12">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>View Abstracts</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            @include('components.admin-alerts')
            <div class="table-responsive mb-4 mt-4">
                <table id="html5-extension" class="table table-hover non-hover" style="width:100%">
                    <thead>
                        <tr>
                            <td>Sr#</td>
                            <th>Paper Number</th>
                            <th>Email</th>
                            <th>Video</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i = 1; @endphp

                        @foreach ($data as $d)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $d->paper }}</td>
                            <td>{{ $d->email }}</td>
    
                            <td>@if($d->document  == '') Pending @else <a href="{{ Storage::URL($d->document) }}" target="_blank">View Video</a> | <a href="{{ Storage::URL($d->document) }}" target="_blank" download>Download</a>  @endif</td>
                         
                        </tr>
                        @endforeach


                    </tbody>
                </table>
            </div>

            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
   <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
   <script src="{{ url('admin/plugins/table/datatable/datatables.js') }}"></script>
   <!-- NOTE TO Use Copy CSV Excel PDF Print Options You Must Include These Files  -->
   <script src="{{ url('admin/plugins/table/datatable/button-ext/dataTables.buttons.min.js') }}"></script>
   <script src="{{ url('admin/plugins/table/datatable/button-ext/jszip.min.js') }}"></script>
   <script src="{{ url('admin/plugins/table/datatable/button-ext/buttons.html5.min.js') }}"></script>
   <script src="{{ url('admin/plugins/table/datatable/button-ext/buttons.print.min.js') }}"></script>
   <script>
       $('#html5-extension').DataTable( {
           dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
               buttons: [
                   { extend: 'copy', className: 'btn' },
                   { extend: 'csv', className: 'btn' },
                   { extend: 'excel', className: 'btn' },
                   { extend: 'print', className: 'btn' }
               ],
           "oLanguage": {
               "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
               "sInfo": "Showing page _PAGE_ of _PAGES_",
               "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
               "sSearchPlaceholder": "Search...",
              "sLengthMenu": "Results :  _MENU_",
           },
           "stripeClasses": [],
           "lengthMenu": [7, 10, 20, 50],
           "pageLength": 7
       } );
   </script>
@endpush
