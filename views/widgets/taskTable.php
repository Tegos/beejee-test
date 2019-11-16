<?php
/**
 * User: Zemlyansky Alexander <astrolog@online.ua>
 */

use app\system\App;

$limit = 3;
$page = $page ?? 1;

$directionContent = [
    'asc' => ' &uarr; ',
    'desc' => ' &darr; '
];

$isAdmin = App::isAdminLogin();

?>

<div class="task-table-container">
    <div class="task-table__info bg-success text-light text-center"></div>
    <div class="task-table__error bg-danger text-light text-center"></div>

    <table class="task-table table" data-page="<?= $page ?>" data-sort="<?= $sort ?>" data-direction="<?= $direction ?>">
        <tr>
            <?php
            foreach (['id' => '#', 'name' => 'User name', 'email' => 'E-mail', 'text' => 'Task text', 'status' => 'Status', 'admin_edited' => 'Was edited by admin'] as $key => $col) {
                if ($sort) {
                    $directionHTML = $direction == 'asc' ? $directionContent['asc'] : $directionContent['desc'];
                    $sortContent = ($key == $sort ? $directionHTML : '');
                    $toggleDirection = $direction == 'asc' ? 'desc' : 'asc';
                }

                echo "<th><a href='#' onclick='taskTable.displayTaskTableAjax(\"$page\", \"$key\", \"$toggleDirection\")'>$col $sortContent</a></th>";
            }
            ?>
        </tr>

        <?php
        foreach ($tasks as $row) {
            $status = [];
            $status[$row['status']] = 'selected';

            $tdParams = (App::isAdminLogin() ? 'contenteditable="true"' : '') . ' onfocus="taskTable.startEdit(this)" onblur="taskTable.cellLeaveEdit(this)" data-id="' . $row['id'] . '"';

            echo '<tr>',
                    "<td>{$row['id']}</td>",
                    "<td $tdParams data-attr=\"name\">{$row['name']}</td>",
                    "<td $tdParams data-attr=\"email\">{$row['email']}</td>",
                    "<td $tdParams data-attr=\"text\">{$row['text']}</td>",
                    '<td>
                        <select ' . $tdParams . '" data-attr="status"' , ($isAdmin ? '' : 'disabled') , '>
                            <option value="1"' , ($status[1] ?? '' ) , '>Done</option>                        
                            <option value="0" ' , ($status[0] ?? '' ) , '>Opened</option>
                        </select>
                    </td>',
                    '<td data-attr="admin_edited" data-id="' . $row['id'] . '">' . ($row['admin_edited'] > 0 ? 'YES' : 'NO') , '</td>',
            '</tr>';
        }
        ?>
    </table>
</div>

<div class="text-center">
    <?php
    if ($totalRows > $limit) {
        for ($i = 1; $i <= ceil($totalRows / $limit); $i++) {
            if ($i != $page) {
                echo '<a class="btn btn-default" onclick="taskTable.displayTaskTableAjax(' . ($i) . ')">' . $i . '</a>';
            } else {
                echo '<a class="btn btn-primary">' . $i . '</a>';
            }
        }
    }
?>
</div>

<script>
    taskTable = (new function() {
        let self = this;

        this.valueBeforeEdit = {};

        this.displayTaskTableAjax = function(page, sort, direction) {
            let tableContainer = document.querySelector('.task-table-container'),
                tableArea = tableContainer.querySelector('.table');

            page = isDefined(page) ? page : tableArea.getAttribute('data-page');
            sort = isDefined(sort) ? sort : tableArea.getAttribute('data-sort');
            direction = isDefined(direction) ? direction : tableArea.getAttribute('data-direction');

            ajax('/displayTaskTableAjax', `page=${page}&sort=${sort}&direction=${direction}`, function(response) {
                tableContainer.parentElement.innerHTML = response;
            }, function(response) {
                document.querySelector('.login-form__error').innerHTML = JSON.parse(response.responseText).join('<BR>')
            });

            event.preventDefault()
        };

        this.cellLeaveEdit = function(el) {
            let attrName = el.getAttribute('data-attr'),
                rowId = el.getAttribute('data-id'),
                value = (attrName !== 'status' ? el.innerHTML : el.value).trim();

            self.activeEdit = el;

            updateTaskField(rowId, attrName, value);

            event.preventDefault();
        }

        let updateTaskField = function(id, name, value) {
            if (value == self.valueBeforeEdit[id][name]) {
                return;
            }

            ajax('/updateTaskField', `id=${id}&name=${name}&value=${value}`, function(response) {
                setInfo(`Successfully edited. Value: "${value}". </b> (Row id: ${id}, Column name: ${name})`);
                setError(null)

                document.querySelector(`.table [data-attr="admin_edited"][data-id="${id}"]`).innerHTML = JSON.parse(response)['rowData']['admin_edited'] > 0 ? 'YES' : 'NO';

            }, function(response) {
                if (name !== 'status') {
                    self.activeEdit.innerHTML = strip_tags(self.valueBeforeEdit[id][name]);
                }

                setError('<b>' + JSON.parse(response.responseText).join('<BR>') + `. </b> Returned to previous value ${self.valueBeforeEdit[id][name]}. (Row id: ${id}, Column name: "${name}")`);
                setInfo(null)
            });
        }

        this.startEdit = function(el) {
            let id = el.getAttribute('data-id'),
                attr = el.getAttribute('data-attr');

            if (!isDefined(this.valueBeforeEdit[id])) {
                this.valueBeforeEdit[id] = {}
            }

            this.valueBeforeEdit[id][attr] = (attr !== 'status' ? el.innerHTML : el.value);
        }

        let setInfo = function(value) {
            document.querySelector('.task-table__info').innerHTML = value
        }

        let setError = function(value) {
            document.querySelector('.task-table__error').innerHTML = value
        }
    });
</script>