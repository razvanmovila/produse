<div class="modal fade editProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= route('update.product') ?>" method="post" id="update-product-form">
                    @csrf
                    <input type="hidden" name="pid">
                    <div class="form-group">
                        <label for="">Name</label>
                        <input type="text" class="form-control" name="name" placeholder="Enter product name">
                        <span class="text-danger error-text name_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="">Price</label>
                        <input type="text" class="form-control" name="price" placeholder="Enter product price">
                        <span class="text-danger error-text price_error"></span>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status">
                            <option>active</option>
                            <option>inactive</option>
                        </select>
                        <span class="text-danger error-text status_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="edit_picture">Product image</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="edit_picture" id="edit_picture">
                                <label class="custom-file-label" for="edit_picture">Choose image (max size: 1 MB)</label>
                            </div>
                            <div class="input-group-append">
                                <span id="clearInputFile" class="input-group-text">Clear</span>
                            </div>
                        </div>
                        <span class="text-danger error-text edit_picture_error"></span>
                    </div>
                    <label id="holderLabelEdit"></label>
                    <div id="holderEdit" class="img-holder-update"></div>
                    <div class="form-group">
                        <button type="submit" id="submitEdit" class="btn btn-block btn-success">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
