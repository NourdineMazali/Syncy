<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
    {{-- data-toggle="modal" data-target="#myModal"--}}
    <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Errors</h4>
            </div>
            <div class="modal-body">
                @foreach ($errors->all() as $message)
                    {!! '<div class="alert alert-danger" role="alert">'.$message.'</div>' !!}
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@if (count($errors) > 0)
    <script type="text/javascript">
        $(window).on('load', function () {
            $('#myModal').modal('show');
        });
    </script>
@endif