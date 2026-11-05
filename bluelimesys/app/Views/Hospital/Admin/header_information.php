

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Administrator</h1>
                <small>Company Header Information</small>
            <ol class="breadcrumb">
                <li class="active"><a href="/hospital/admin/">Dashboard</a></li>
            </ol>
        </div>
    </section>

    <section class="content">
        <?php
            helper('form');
            $headerList = $header_info ?? [];
            $editHeaderList = $edit_header_info ?? [];
            $totalHeaders = is_array($headerList) ? count($headerList) : 0;
            $activeHeaders = 0;
            if (is_array($headerList)) {
                foreach ($headerList as $item) {
                    if (!empty($item['active'])) {
                        $activeHeaders++;
                    }
                }
            }
            $isEditing = !empty($editHeaderList[0]);
            $currentHeader = $isEditing ? $editHeaderList[0] : [];
        ?>

        <style>
            .header-summary {
                border-left: 4px solid #1f7a8c;
                background: #f7fbfc;
                padding: 14px 18px;
                margin-bottom: 20px;
            }
            .header-summary h3 {
                color: #164e63;
                margin: 0 0 4px;
                font-weight: 600;
            }
            .header-summary p {
                color: #64748b;
                margin: 0;
            }
            .header-form .form-group {
                margin-bottom: 16px;
            }
            .header-form .control-label {
                color: #334155;
                font-weight: 600;
                margin-bottom: 6px;
            }
            .logo-preview {
                display: inline-block;
                margin-top: 8px;
                border: 1px solid #dbe4e8;
                padding: 5px;
                background: #fff;
            }
            .profile-actions .btn {
                margin-left: 4px;
            }
        </style>


        <div class="row">
            <div class="col-md-7">
                <div class="panel panel-bd panel-form">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Header Profiles</h4>
                        </div>
                    </div>
                    <div class="panel-body table-responsive">
                        <?php if (!empty($headerList)): ?>
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($headerList as $headerinfo): ?>
                                        <tr>
                                            <td>
                                                <strong><?= !empty($headerinfo['company_name']) ? $headerinfo['company_name'] : 'Unnamed Company' ?></strong>
                                            </td>
                                            <td><?= !empty($headerinfo['email']) ? $headerinfo['email'] : '—' ?></td>
                                            <td><?= !empty($headerinfo['mobile']) ? $headerinfo['mobile'] : '—' ?></td>
                                            <!-- <td>
                                                <?php if (!empty($headerinfo['active'])): ?>
                                                    <span class="label label-success">Active</span>
                                                <?php else: ?>
                                                    <span class="label label-warning">Inactive</span>
                                                <?php endif; ?>
                                            </td> -->
                                            <td class="text-right">
                                                <div class="profile-actions">
                                                    <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#headerProfileModal"
                                                        data-company="<?= htmlspecialchars($headerinfo['company_name'] ?? 'Unnamed Company', ENT_QUOTES, 'UTF-8') ?>"
                                                        data-email="<?= htmlspecialchars($headerinfo['email'] ?? 'Not provided', ENT_QUOTES, 'UTF-8') ?>"
                                                        data-mobile="<?= htmlspecialchars($headerinfo['mobile'] ?? 'Not provided', ENT_QUOTES, 'UTF-8') ?>"
                                                        data-tel="<?= htmlspecialchars($headerinfo['tel'] ?? 'Not provided', ENT_QUOTES, 'UTF-8') ?>"
                                                        data-website="<?= htmlspecialchars($headerinfo['website'] ?? 'Not provided', ENT_QUOTES, 'UTF-8') ?>"
                                                        data-tin="<?= htmlspecialchars($headerinfo['tin_no'] ?? 'Not provided', ENT_QUOTES, 'UTF-8') ?>"
                                                        data-location="<?= htmlspecialchars($headerinfo['location'] ?? 'Not provided', ENT_QUOTES, 'UTF-8') ?>">
                                                        <i class="fa fa-eye"></i> View
                                                    </button>
                                                    <a href="<?= site_url('hospital/admin/headerinfo') ?>?hid=<?= $headerinfo['id'] ?>&edit=1" class="btn btn-info btn-xs">
                                                        <i class="fa fa-edit"></i> Edit
                                                    </a>
                                                    <?= form_open('hospital/admin/changeheaderstatus', ['class' => 'd-inline-block', 'style' => 'display: inline-block;']); ?>
                                                        <input type="hidden" name="header_id" value="<?= $headerinfo['id'] ?>">
                                                        <input type="hidden" name="active" value="<?= !empty($headerinfo['active']) ? 1 : 0 ?>">
                                                        <button type="submit" class="btn btn-xs <?= !empty($headerinfo['active']) ? 'btn-success' : 'btn-warning' ?>" title="<?= !empty($headerinfo['active']) ? 'Disable profile' : 'Enable profile' ?>">
                                                            <i class="fa <?= !empty($headerinfo['active']) ? 'fa-toggle-on' : 'fa-toggle-off' ?>"></i>
                                                            <?= !empty($headerinfo['active']) ? 'Enabled' : 'Disabled' ?>
                                                        </button>
                                                    <?= form_close(); ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-info" style="margin-bottom: 0;">
                                No header profile has been created yet.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="panel panel-bd panel-form">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4><?= $isEditing ? 'Edit Header Information' : 'Add Header Information' ?></h4>
                        </div>
                    </div>
                    <div class="panel-body table-responsive header-form">
                        <div class="col-md-12"> 
                        <?php if (!$isEditing): ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i>
                                Enter a new company header profile.
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fa fa-pencil"></i>
                                You are editing <strong><?= htmlspecialchars($currentHeader['company_name'] ?? 'this profile', ENT_QUOTES, 'UTF-8') ?></strong>.
                            </div>
                        <?php endif; ?>
                        <?= form_open_multipart('hospital/admin/addheader', ['class' => 'form-horizontal', 'method' => 'post', 'enctype' => 'multipart/form-data', 'id' => 'headerInformationForm']); ?>

                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label">Company Name</label>
                                <div>
                                    <input type="text" name="company_name" class="form-control" required value="<?= !empty($currentHeader['company_name']) ? $currentHeader['company_name'] : '' ?>" placeholder="Company Name">
                                    <input type="hidden" name="id" value="<?= !empty($currentHeader['id']) ? $currentHeader['id'] : '' ?>">
                                    <input type="hidden" name="edit" value="<?= $isEditing ? 1 : 0 ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Telephone</label>
                                <div>
                                    <input type="text" name="tel" class="form-control" value="<?= !empty($currentHeader['tel']) ? $currentHeader['tel'] : '' ?>" placeholder="Telephone">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Mobile</label>
                                <div>
                                    <input type="text" name="mobile" class="form-control" value="<?= !empty($currentHeader['mobile']) ? $currentHeader['mobile'] : '' ?>" placeholder="Mobile">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Email</label>
                                <div>
                                    <input type="email" name="email" class="form-control" value="<?= !empty($currentHeader['email']) ? $currentHeader['email'] : '' ?>" placeholder="Email Address">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Website</label>
                                <div>
                                    <input type="text" name="website" class="form-control" value="<?= !empty($currentHeader['website']) ? $currentHeader['website'] : '' ?>" placeholder="Website URL">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">TIN Number</label>
                                <div>
                                    <input type="text" name="tin_no" class="form-control" value="<?= !empty($currentHeader['tin_no']) ? $currentHeader['tin_no'] : '' ?>" placeholder="TIN Number">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Location</label>
                                <div>
                                    <input type="text" name="location" class="form-control" value="<?= !empty($currentHeader['location']) ? $currentHeader['location'] : '' ?>" placeholder="Physical Location">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Primary Logo</label>
                                <div>
                                    <input type="file" name="logo" class="form-control" accept="image/*">
                                    <?php if (!empty($currentHeader['logo'])): ?>
                                        <div class="m-t-10">
                                            <img src="data:image/jpeg;base64,<?= base64_encode($currentHeader['logo']) ?>" style="max-height: 60px;" alt="Company logo">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Small Logo</label>
                                <div>
                                    <input type="file" name="logo_small" class="form-control" accept="image/*">
                                    <?php if (!empty($currentHeader['logo_small'])): ?>
                                        <div class="m-t-10">
                                            <img src="data:image/jpeg;base64,<?= base64_encode($currentHeader['logo_small']) ?>" style="max-height: 50px;" alt="Small company logo">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Printout Logo</label>
                                <div>
                                    <input type="file" name="logo_printouts" class="form-control" accept="image/*">
                                    <?php if (!empty($currentHeader['logo_printouts'])): ?>
                                        <div class="m-t-10">
                                            <img src="data:image/jpeg;base64,<?= base64_encode($currentHeader['logo_printouts']) ?>" style="max-height: 50px;" alt="Printout company logo">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row" style="margin-top: 20px;">
                            <div class="col-sm-12 text-right">
                                <button type="reset" class="btn btn-default">Reset</button>
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Information</button>
                            </div>
                        </div>

                        <?= form_close(); ?>
                    </div>
                    </div> 
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="headerProfileModal" tabindex="-1" role="dialog" aria-labelledby="headerProfileModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #164e63; color: #fff;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="headerProfileModalLabel">Header Profile</h4>
            </div>
            <div class="modal-body">
                <dl class="dl-horizontal" style="margin-bottom: 0;">
                    <dt>Company</dt><dd data-profile-field="company"></dd>
                    <dt>Email</dt><dd data-profile-field="email"></dd>
                    <dt>Telephone</dt><dd data-profile-field="tel"></dd>
                    <dt>Mobile</dt><dd data-profile-field="mobile"></dd>
                    <dt>Website</dt><dd data-profile-field="website"></dd>
                    <dt>TIN Number</dt><dd data-profile-field="tin"></dd>
                    <dt>Location</dt><dd data-profile-field="location"></dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('#headerProfileModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            ['company', 'email', 'tel', 'mobile', 'website', 'tin', 'location'].forEach(function (field) {
                modal.find('[data-profile-field="' + field + '"]').text(button.data(field));
            });
        });

        $('#headerInformationForm').on('submit', function (event) {
            var email = $.trim($(this).find('[name="email"]').val());
            var phone = $.trim($(this).find('[name="tel"]').val()) || $.trim($(this).find('[name="mobile"]').val());

            if (!phone) {
                event.preventDefault();
                alert('Please provide at least one telephone or mobile number.');
                $(this).find('[name="tel"]').focus();
                return false;
            }

            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                event.preventDefault();
                alert('Please enter a valid email address.');
                $(this).find('[name="email"]').focus();
                return false;
            }
        });
    });
</script>
                             
            
    </section>

</div>