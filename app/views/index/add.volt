<!-- Contact Section -->
<section id="addExt">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h3>Add Phalcon Resource</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
                <form name="addExt" method="post" action="/add">
                    <div class="row control-group">
                        <div class="form-group col-xs-12 floating-label-form-group controls">
                            <label>GitHub URL</label>
                            <input type="text" name="url" placeholder="GitHub URL" class="form-control" id="url"
                                   required data-validation-required-message="Please enter your GitHub URL.">

                            <p class="help-block text-danger"></p>
                        </div>
                    </div>
                    <br>

                    <div id="success"></div>
                    <div class="row">
                        <div class="form-group col-xs-12">
                            <button type="submit" class="btn btn-success btn-lg">Send</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
