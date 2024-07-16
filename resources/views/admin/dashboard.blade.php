@extends('master.admin-master')

@section('title','Dashboard')
@section('content')

Dashboard

@endsection

@push('scripts')
<script>
    jQuery(function($) {
var fbEditor = document.getElementById('build-wrap'),
options = {
    onSave: function(evt, formData) {
      },
  typeUserAttrs: {
    date: {
      min: {
        label: 'Date min.',
        maxlength: '10',
        description: 'Minimum'
      },
      max: {
        label: 'Date max.',
        maxlength: '10',
        onclick: 'alert("wooohoooo")',
        placeholder: 'yeah "sure" whateverman'
      }
    },
    text: {
      className: {
        label: 'Class',
        options: {
          'red form-control': 'Red',
          'green form-control': 'Green',
          'blue form-control': 'Blue'
        },
        style: 'border: 1px solid red'
      }
    }
  }
};
$(fbEditor).formBuilder(options);
});
</script>
@endpush
