<?php
/**
 * Created by PhpStorm.
 * User: narims
 * Date: 03.07.18
 * Time: 12:22
 */

namespace common\services;

use common\models\Employee;
use common\models\EmployeeFilial;
use common\models\Position;
use Yii;


class EmployeeService
{
    const SAVE_FROM_SELECT2 = 'select2';
    const SAVE_FROM_TREE = 'tree';

    const EMPLOYEE_NAME = 0;
    const EMPLOYMENT_DATE = 1;
    const CITY = 2;
    const FILIAL = 3;
    const POSITION = 4;
    const SCHEDULE = 5;
    const COMMENT = 6;

    /**
     * @param Employee $model
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function save($model, $type = self::SAVE_FROM_TREE)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($type === self::SAVE_FROM_TREE) {
                $filialIds = explode(",", $model->filialCheckboxes);
            } else {
                $filialIds = $model->filialCheckboxes;
            }
            $model->save();
            EmployeeFilial::deleteAll(['employee_id' => $model->id]);
            foreach ($filialIds as $filialId) {
                $employeeFilial = new EmployeeFilial();
                $employeeFilial->employee_id = $model->id;
                $employeeFilial->filial_id = $filialId;
                $employeeFilial->save();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Названия объектов(филиалов) сотрудника
     * @param $model
     * @return string
     */
    public static function getFilialsName($model)
    {

        if (empty($model->filials) || !is_array($model->filials)) {
            return Yii::t('app', 'Не задано');
        }

        $names = [];
        foreach ($model->filials as $filial) {
            $names[] = $filial->name;
        }

        return implode("; ", $names);
    }

    public function import($item, $client_id)
    {
        if ($this->validateImportItem($item)) {
            return $this->createOrUpdateEmployee($item, $client_id);
        } else {
            Yii::info("CSV Employee NOT SAVED: {$item[self::EMPLOYEE_NAME]}. Validation FAILED");
            return false;
        }
    }


    private function createOrUpdateEmployee($item, $client_id)
    {
        $filial = Yii::$app->filialService->getFilialByName($item[self::FILIAL], $client_id, $item[self::CITY]);
        if ($filial) {
            $position = $this->getOrCreatePosition($item[self::POSITION]);
            if (!$position) {
                Yii::info("CSV Employee NOT SAVED: {$item[self::EMPLOYEE_NAME]}. POSITION CREATION FAILED");
                return false;
            }
            $employee = $this->getEmployeeByName($item[self::EMPLOYEE_NAME], $client_id);
            if (!$employee) {
                $newEmployee = new Employee();
                $newEmployee->name = $item[self::EMPLOYEE_NAME];
                $newEmployee->employment_date = $item[self::EMPLOYMENT_DATE] ?: date('Y-m-d');
                $newEmployee->schedule = $item[self::SCHEDULE] ?: null;
                $newEmployee->comment = $item[self::COMMENT] ?: null;
                $newEmployee->position_id = $position->id;
                $newEmployee->filialCheckboxes = 0;
                if ($newEmployee->save()) {
                    $employeeFilial = new EmployeeFilial();
                    $employeeFilial->employee_id = $newEmployee->id;
                    $employeeFilial->filial_id = $filial->id;
                    if ($employeeFilial->save()) {
                        Yii::info("CSV Employee SAVED: {$item[self::EMPLOYEE_NAME]}, LINKED TO: {$item[self::FILIAL]} id={$filial->id}.");
                        return $newEmployee;
                    } else {
                        Yii::info("CSV Employee EMPLOYEE-FILIAL LINK FAILURE. RECORD IS NOT SAVED: {$item[self::EMPLOYEE_NAME]},
                         LINKED TO: {$item[self::FILIAL]} id={$filial->id}.");
                        return false;
                    }
                }
            } else {
                $employee->employment_date = $item[self::EMPLOYMENT_DATE] ?: date('Y-m-d');
                $employee->schedule = $item[self::SCHEDULE] ?: null;
                $employee->comment = $item[self::COMMENT] ?: null;
                $employee->position_id = $position->id;
                if ($employee->save() && $this->manageEmployeeFilialLink($employee->id, $filial->id)) {
                    Yii::info("CSV Employee UPDATED: {$item[self::EMPLOYEE_NAME]}, {$item[self::FILIAL]}.");
                    return $employee;
                }
            }
        } else {
            Yii::info("CSV Employee NOT SAVED: {$item[self::EMPLOYEE_NAME]}, {$item[self::FILIAL]} - FILIAL NOT EXISTS.");
            return false;
        }
    }

    private function manageEmployeeFilialLink(int $employee_id, int $filial_id)
    {
        $result = EmployeeFilial::findAll(['employee_id' => $employee_id, 'filial_id' => $filial_id]);
        if ($result) {
            return true;
        } else {
            $employeeFilial = new EmployeeFilial();
            $employeeFilial->employee_id = $employee_id;
            $employeeFilial->filial_id = $filial_id;
            return $employeeFilial->save();
        }
    }

    private function validateImportItem(&$item)
    {
        foreach ($item as $i => &$val) {
            switch ($i) {
                case self::EMPLOYEE_NAME:
                    if (!$val) {
                        return false;
                    }
                    break;
                case self::EMPLOYMENT_DATE:
                    if ($val) {
                        $date = strtotime($val);
                        $val = date('Y-m-d', $date);
                    }
                    break;
                case self::CITY:
                    if (!$val) {
                        return false;
                    }
                    break;
                case self::FILIAL:
                    if (!$val) {
                        return false;
                    }
                    break;
                case self::POSITION:
                    if (!$val) {
                        return false;
                    }
                    break;
            }
        }
        return true;
    }

    public function getEmployeeByName($name, $client_id)
    {
        $query = Employee::find()->where(['like', 'employee.name', $name])
            ->innerJoin('employee_filial', "employee.id = employee_filial.employee_id")
            ->innerJoin('filial', "(employee_filial.filial_id = filial.id and filial.parent_id = {$client_id})")//            ->andWhere(['employee_filial.filial_id' => $filial_id])
        ;
        return $query->one();
    }


    private function getOrCreatePosition($name)
    {
        $position = Position::find()->where(['like', 'name', $name])->one();
        if (!$position) {
            $newPosition = new Position();
            $newPosition->name = $name;
            if ($newPosition->save()) {
                Yii::info("CSV Employee UPLOAD -->>> CREATED NEW POSITION: {$name}.");
                return $newPosition;
            } else {
                Yii::info("CSV Employee UPLOAD -->>> CANNOT CREATE NEW POSITION: {$name}.");
                return false;
            }
        } else {
            return $position;
        }
    }


}