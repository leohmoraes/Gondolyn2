<!-- Modal -->
<div class="modal fade" id="adminDeleteModal" tabindex="-2" role="dialog" aria-labelledby="adminDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="adminDeleteModalLabel">Delete User Account</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you wish to delete this account? Please remember you cannot undo this action.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <a id="deleteBtn" type="button" class="btn btn-danger" href="{!! URL::to('admin/users/delete') !!}">Confirm Delete</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="adminDeactivateModal" tabindex="-3" role="dialog" aria-labelledby="adminDeactivateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="adminDeactivateModalLabel">Deactivate User Account</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you wish to deactivate this account? The user will not be able to log in.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <a id="deleteBtn" type="button" class="btn btn-warning" href="{!! URL::to('admin/users/deactivate') !!}">Confirm Deactivation</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="adminActivateModal" tabindex="-4" role="dialog" aria-labelledby="adminActivateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="adminActivateModalLabel">Activate User Account</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you wish to activate this account?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <a id="deleteBtn" type="button" class="btn btn-success" href="{!! URL::to('admin/users/activate') !!}">Confirm Activation</a>
            </div>
        </div>
    </div>
</div>