<?php

use app\system\App;
use app\system\View;

?>

<div class="container-fluid text-center mt-5">
    <h1>Tasks</h1>

    <div class="row text-left">
        <div class="col-sm-12">

            <?php View::render('widgets/taskTable', [
                'tasks' => $tasks,
                'totalRows' => $totalRows,
                'sort' => $sort,
                'direction' => $direction
            ]) ?>

        </div>
    </div>

    <hr />

    <div>
        <h2>Create a new task</h2>

        <div class="col-sm-12 mb-1">
            <div class="create-task-form__info bg-success text-light"></div>
            <div class="create-task-form__error bg-danger text-light"></div>
        </div>

        <form class="create-task-form" class="text-left" method="post">
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label>Name</label><input name="name" class="form-control">
                </div>
                <div class="form-group col-md-3">
                    <label>E-mail</label><input name="email" class="form-control">
                </div>
                <div class="form-group col-md-3">
                    <label>Text</label><textarea name="text" class="form-control"></textarea>
                </div>
                <div class="form-group col-md-3 mt-3">
                    <button class="btn btn-default" class="form-control" onclick="taskActions.createTask()">Create task</button>
                </div>
            </div>
        </form>
    </div>

</div>


<script>
    taskActions = (new function() {
        this.createTask = function() {
            let form = document.querySelector('.create-task-form')

            event.preventDefault()

            ajax('/createTask', 'name=' + form['name'].value + '&email=' + form['email'].value + '&text=' + form['text'].value, function(response) {
                setInfo('The task was successfully added');
                setError(null);

                for (let input of form.querySelectorAll('input,textarea')) {
                    input.value = null;
                }

                taskTable.displayTaskTableAjax();

            }, function(response) {
                setError(JSON.parse(response.responseText).join('<BR>'));
                setInfo(null);
            });

            let setInfo = function(value) {
                document.querySelector('.create-task-form__info').innerHTML = value
            }

            let setError = function(value) {
                document.querySelector('.create-task-form__error').innerHTML = value;
                setTimeout(function() {
                    document.querySelector('.create-task-form__error').innerHTML = ''
                }, 5000)
            }

        }
    });
</script>
