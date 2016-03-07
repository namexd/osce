<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016-03-07 14:11
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */
Route::group(['prefix' => "osce", 'namespace' => 'Modules\Osce\Http\Controllers', 'middleware' => []], function () {

    Route::group(['prefix' => 'admin', 'namespace' => 'Admin\Branch'], function () {
            //TODO �ƿ�
            Route::get('subject-statistics/subject-grade-list',['uses'=>'SubjectStatisticsController@SubjectGradeList','as'=>'osce.admin.SubjectStatisticsController.SubjectGradeList']);
            //������Ŀͳ��
            Route::get('testscores/test-score-list',['uses'=>'TestScoresController@TestScoreList','as'=>'osce.admin.TestScoresController.TestScoreList']);
            //�����ɼ�ͳ��
            Route::get('testscores/student-subject-list',['uses'=>'TestScoresController@studentSubjectList','as'=>'osce.admin.TestScoresController.studentSubjectList']);
            //ajax��ȡ�����������ÿ�Ŀ
            Route::get('testscores/ajax-get-subject',['uses'=>'TestScoresController@getAjaxGetSubject','as'=>'osce.admin.TestScoresController.getAjaxGetSubject']);
            Route::get('testscores/ajax-get-subjectlist',['uses'=>'TestScoresController@ajaxGetSubjectlist','as'=>'osce.admin.TestScoresController.ajaxGetSubjectlist']);
            //ajax��ȡ�����������ÿ�Ŀ�ɼ�
            Route::get('testscores/ajax-get-student-test-count',['uses'=>'TestScoresController@ajaxGetStudentTestCount','as'=>'osce.admin.TestScoresController.ajaxGetStudentTestCount']);
            //���Կ�Ŀ��ϸ��Ϣ��ѯ
            Route::get('subject-statistics/subject-info',['uses'=>'SubjectStatisticsController@SubjectGradeInfo','as'=>'osce.admin.SubjectStatisticsController.SubjectGradeInfo']);
            //���Կ�Ŀ�Ѷȷ���
            Route::get('subject-statistics/subject-analyze',['uses'=>'SubjectStatisticsController@SubjectGradeAnalyze','as'=>'osce.admin.SubjectStatisticsController.SubjectGradeAnalyze']);
            //�����ɼ�����
            Route::get('testscores/get-tester-score-detail',['uses'=>'TestScoresController@getTesterScoreDetail','as'=>'osce.admin.TestScoresController.getTesterScoreDetail']);
            //��վ�ɼ�����
            Route::get('subject-statistics/station-grade-list',['uses'=>'SubjectStatisticsController@stationGradeList','as'=>'osce.admin.SubjectStatisticsController.stationGradeList']);

            //��վ�ɼ�����-����
            Route::get('subject-statistics/stationDetails',['uses'=>'SubjectStatisticsController@stationDetails','as'=>'osce.admin.SubjectStatisticsController.stationDetails']);



            //���˵����
            Route::get('subject-statistics/standard-grade-list',['uses'=>'SubjectStatisticsController@standardGradeList','as'=>'osce.admin.SubjectStatisticsController.standardGradeList']);
            Route::get('subject-statistics/subject',['uses'=>'SubjectStatisticsController@getSubject','as'=>'osce.admin.SubjectStatisticsController.getSubject']);
            Route::post('testscores/ajax-get-tester',['uses'=>'TestScoresController@postAjaxGetTester','as'=>'osce.admin.TestScoresController.postAjaxGetTester']);
            //���˵�ɼ�����-����
            Route::get('subject-statistics/standardDetails',['uses'=>'SubjectStatisticsController@standardDetails','as'=>'osce.admin.SubjectStatisticsController.standardDetails']);
            //��ѧ�ɼ�����
            Route::get('testscores/test-scores-count',['uses'=>'TestScoresController@testScoresCount','as'=>'osce.admin.TestScoresController.testScoresCount']);
            //��ѧ�ɼ�����-��ȡ��Ŀ
            Route::get('testscores/subject-lists',['uses'=>'TestScoresController@getSubjectLists','as'=>'osce.admin.TestScoresController.getSubjectLists']);
            //��ѧ�ɼ�����-��ȡ��ȡ�����б�����
            Route::get('testscores/teacher-data-list',['uses'=>'TestScoresController@getTeacherDataList','as'=>'osce.admin.TestScoresController.getTeacherDataList']);
            //��ѧ�ɼ�����-�༶��ʷ�ɼ�
            Route::get('testscores/grade-score-list',['uses'=>'TestScoresController@getGradeScoreList','as'=>'osce.admin.TestScoresController.getGradeScoreList']);
            //��ѧ�ɼ�����-�༶�ɼ���ϸ
            Route::get('testscores/grade-detail',['uses'=>'TestScoresController@getGradeDetail','as'=>'osce.admin.TestScoresController.getGradeDetail']);


    });

});