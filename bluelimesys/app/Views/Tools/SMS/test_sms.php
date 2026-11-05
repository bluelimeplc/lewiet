<?php
    $result = $this->data['result'];
?>
<div class="row">
    <div class="col-sm-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Test SMS</h5>

                <!-- Floating Labels Form -->
                <form class="row g-3" method="post">
                    <div class="col-md-12">
                        <div class="form-floating">
                        <input type="text" name="mobile" class="form-control" id="floatingName" placeholder="Mobile">
                        <label for="floatingName">Mobile</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-floating">
                        <textarea name="message" class="form-control" placeholder="Address" id="floatingTextarea" style="height: 100px;"></textarea>
                        <label for="floatingTextarea">Message</label>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Send</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                    </div>
                </form><!-- End floating Labels Form -->

            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="card">
            <div class="card-body" style="min-height: 200px;">
                <h5 class="card-title">Result<hr></h5>
                <?= $result; ?>
            </div>
        </div>
    </div>
</div>