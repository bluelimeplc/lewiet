<?php
$filters = $filters ?? [];
$trailRows = is_array($rows ?? null) ? $rows : [];
$tableNames = $table_names ?? [];
$models = $models ?? [];
$users = $users ?? [];
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-note2"></i>
        </div>
        <div class="header-title">
            <h1>Audit Trail Report</h1>
            <small>Review data changes by date, table, model, and user</small>
            <ol class="breadcrumb">
                <?= view("Include/_admin_report_top_menu"); ?>
            </ol>
        </div>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd lobidisable">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Filter Trail</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <form action="/hospital/report/audittrailreport" method="get" class="form-inline">
                        <div class="form-group" style="margin-right:10px; margin-bottom:10px;">
                            <label for="from_date">From</label>
                            <input type="date" class="form-control" id="from_date" name="from_date" value="<?= esc($filters['from_date'] ?? '') ?>">
                        </div>
                        <div class="form-group" style="margin-right:10px; margin-bottom:10px;">
                            <label for="to_date">To</label>
                            <input type="date" class="form-control" id="to_date" name="to_date" value="<?= esc($filters['to_date'] ?? '') ?>">
                        </div>
                        <div class="form-group" style="margin-right:10px; margin-bottom:10px;">
                            <label for="table_name">Table</label>
                            <select class="form-control" id="table_name" name="table_name">
                                <option value="">All tables</option>
                                <?php foreach ($tableNames as $tableName): ?>
                                    <option value="<?= esc($tableName) ?>" <?= ($filters['table_name'] ?? '') === $tableName ? 'selected' : '' ?>><?= esc($tableName) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-right:10px; margin-bottom:10px;">
                            <label for="model">Model</label>
                            <select class="form-control" id="model" name="model">
                                <option value="">All models</option>
                                <?php foreach ($models as $modelName): ?>
                                    <option value="<?= esc($modelName) ?>" <?= ($filters['model'] ?? '') === $modelName ? 'selected' : '' ?>><?= esc($modelName) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-right:10px; margin-bottom:10px;">
                            <label for="user_name">User</label>
                            <select class="form-control" id="user_name" name="user_name">
                                <option value="">All users</option>
                                <?php foreach ($users as $userName): ?>
                                    <option value="<?= esc($userName) ?>" <?= ($filters['user_name'] ?? '') === $userName ? 'selected' : '' ?>><?= esc($userName) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-right:10px; margin-bottom:10px;">
                            <label for="action">Action</label>
                            <select class="form-control" id="action" name="action">
                                <option value="">All actions</option>
                                <?php foreach ($actions as $actionName): ?>
                                    <option value="<?= esc($actionName) ?>" <?= ($filters['action'] ?? '') === $actionName ? 'selected' : '' ?>><?= esc(strtoupper($actionName)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success" style="margin-bottom:10px;">Apply</button>
                        <a href="/hospital/report/audittrailreport" class="btn btn-default" style="margin-bottom:10px;">Reset</a>
                        <a href="/hospital/report/exportaudittrailreport?<?= http_build_query($filters) ?>" class="btn btn-info" style="margin-bottom:10px;">Export CSV</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd lobidisable">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Trail Entries</h4>
                        </div>
                    </div>
                    <div class="panel-body table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Time</th>
                                    <th>Action</th>
                                    <th>Table</th>
                                    <th>Model</th>
                                    <th>User</th>
                                    <th>Row</th>
                                    <th>Difference</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($trailRows)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No audit entries found for the selected filters.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $serial = 1; foreach ($trailRows as $row): ?>
                                        <tr>
                                            <td><?= $serial++ ?></td>
                                            <td><?= esc($row['created_at'] ?? '') ?></td>
                                            <td><span class="label label-<?= ($row['action'] ?? 'update') === 'delete' ? 'danger' : (($row['action'] ?? 'update') === 'insert' ? 'success' : 'info') ?>"><?= esc(strtoupper($row['action'] ?? 'UPDATE')) ?></span></td>
                                            <td><?= esc($row['table_name'] ?? '') ?></td>
                                            <td><?= esc(($row['model'] ?? '') !== '' ? basename(str_replace('\\', '/', (string) ($row['model'] ?? ''))) : '') ?></td>
                                            <td><?= esc($row['user_name'] ?? ($row['user_id'] ?? '')) ?></td>
                                            <td><?= esc($row['row_id'] ?? '') ?></td>
                                            <td>
                                                <?php if (empty($row['difference'])): ?>
                                                    <span class="text-muted">No field-level difference recorded.</span>
                                                <?php else: ?>
                                                    <ul class="mb-0">
                                                        <?php foreach ($row['difference'] as $change): ?>
                                                            <li>
                                                                <strong><?= esc($change['field'] ?? '') ?></strong>
                                                                <?php if (($change['action'] ?? '') === 'added'): ?>
                                                                    <span class="text-success">added</span>
                                                                <?php elseif (($change['action'] ?? '') === 'removed'): ?>
                                                                    <span class="text-danger">removed</span>
                                                                <?php else: ?>
                                                                    <span class="text-warning">changed</span>
                                                                <?php endif; ?>
                                                                <br>
                                                                <small>Old: <?= esc($change['old_value'] ?? '—') ?></small><br>
                                                                <small>New: <?= esc($change['new_value'] ?? '—') ?></small>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
