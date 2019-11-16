<?php
/**
 * User: Zemlyansky Alexander <astrolog@online.ua>
 */

namespace app\Controllers;

use app\models\Task;
use app\system\App;

/**
 * Class SiteController
 * Working with tasks
 *
 * @package app\Controllers
 */
class SiteController extends \app\system\Controller {

    public function actionIndex() {
        $model = new Task();

        $tasks = $model->select('*', null, 'id DESC', 3);

        $this->render('index', [
            'tasks' => $tasks ?? [],
            'totalRows' => $model->count(),
            'sort' => 'id',
            'direction' => 'desc'
        ]);
    }

    public function actionDisplayTaskTableAjax() {
        $model = new Task();

        $orderInfo = ($_POST['sort'] ?? 'id') . ' ' . ($_POST['direction'] ?? 'desc');

        $tasks = $model->select('*', null, $orderInfo, 3, ($_POST['page'] - 1) * 3);

        $this->render('widgets/taskTable', [
            'tasks' => $tasks ?? [],
            'totalRows' => $model->count(),
            'page' => $_POST['page'],
            'sort' => $_POST['sort'],
            'direction' => $_POST['direction']
        ]);
    }

    public function actionCreateTask() {
        $model = new Task();

        if (!$model->validate($_POST)) {
            $this->error404();
            echo json_encode($model->getErrors());
            return;
        }

        return $model->insert([
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'text' => strip_tags($_POST['text']),
            'status' => $_POST['status'] ?? 0
        ]);
    }

    public function actionUpdateTaskField() {
        if (!App::isAdminLogin()) {
            $this->error404();
            echo json_encode(['Permission denied']);
            return;
        }

        $model = new Task();
        $task = $model->select('*', 'id = ' . $_POST['id'])[0];

        $task[$_GET['name']] = $_GET['value'];

        if (!$model->validate($task)) {
            $this->error404();
            echo json_encode($model->getErrors());
            return;
        }

        // we don't have events in our MVC (i.e. BEFORE_UPDATE), so we put it there
        $admin_edited = ($task['admin_edited'] > 0 || $_POST['name'] == 'text' && $_POST['text'] !== $task['text']) ? 1 : 0;

        $model->update([
            'name' => strip_tags($task['name']),
            'email' => strip_tags($task['email']),
            'text' => $task['text'],
            'status' => $task['status'] ?? 0,
            'admin_edited' => $admin_edited
        ], 'id = ' . $task['id']);

        // обновим не долго думая
        $task = $model->select('*', 'id = ' . $_POST['id'])[0];

        echo json_encode([
            'rowData' => $task
        ]);
    }

}