<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016-03-07 14:11
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */
Route::group(['prefix' => "osce", 'namespace' => 'Modules\Osce\Http\Controllers', 'middleware' => []], function () {

    Route::group(['prefix' => 'admin', 'namespace' => 'Admin\Branch'], function () {
        //TODO 唐俊
        Route::get('subject-statistics/subject-grade-list',['uses'=>'SubjectStatisticsController@SubjectGradeList','as'=>'osce.admin.SubjectStatisticsController.SubjectGradeList']);
        //考生科目统计
        Route::get('testscores/test-score-list',['uses'=>'TestScoresController@TestScoreList','as'=>'osce.admin.TestScoresController.TestScoreList']);
        //考生成绩统计
        Route::get('testscores/student-subject-list',['uses'=>'TestScoresController@studentSubjectList','as'=>'osce.admin.TestScoresController.studentSubjectList']);
        //ajax获取考生所考过得科目
        Route::get('testscores/ajax-get-subject',['uses'=>'TestScoresController@getAjaxGetSubject','as'=>'osce.admin.TestScoresController.getAjaxGetSubject']);
        Route::get('testscores/ajax-get-subjectlist',['uses'=>'TestScoresController@ajaxGetSubjectlist','as'=>'osce.admin.TestScoresController.ajaxGetSubjectlist']);
        //ajax获取考生所考过得科目成绩
        Route::get('testscores/ajax-get-student-test-count',['uses'=>'TestScoresController@ajaxGetStudentTestCount','as'=>'osce.admin.TestScoresController.ajaxGetStudentTestCount']);
        //考试科目详细信息查询
        Route::get('subject-statistics/subject-info',['uses'=>'SubjectStatisticsController@SubjectGradeInfo','as'=>'osce.admin.SubjectStatisticsController.SubjectGradeInfo']);
        //考试科目难度分析
        Route::get('subject-statistics/subject-analyze',['uses'=>'SubjectStatisticsController@SubjectGradeAnalyze','as'=>'osce.admin.SubjectStatisticsController.SubjectGradeAnalyze']);
        //考生成绩详情
        Route::get('testscores/get-tester-score-detail',['uses'=>'TestScoresController@getTesterScoreDetail','as'=>'osce.admin.TestScoresController.getTesterScoreDetail']);
        //考站成绩分析
        Route::get('subject-statistics/station-grade-list',['uses'=>'SubjectStatisticsController@stationGradeList','as'=>'osce.admin.SubjectStatisticsController.stationGradeList']);

        //考站成绩分析-详情
        Route::get('subject-statistics/stationDetails',['uses'=>'SubjectStatisticsController@stationDetails','as'=>'osce.admin.SubjectStatisticsController.stationDetails']);



        //考核点分析
        Route::get('subject-statistics/standard-grade-list',['uses'=>'SubjectStatisticsController@standardGradeList','as'=>'osce.admin.SubjectStatisticsController.standardGradeList']);
        Route::get('subject-statistics/subject',['uses'=>'SubjectStatisticsController@getSubject','as'=>'osce.admin.SubjectStatisticsController.getSubject']);
        Route::post('testscores/ajax-get-tester',['uses'=>'TestScoresController@postAjaxGetTester','as'=>'osce.admin.TestScoresController.postAjaxGetTester']);
        //考核点成绩分析-详情
        Route::get('subject-statistics/standardDetails',['uses'=>'SubjectStatisticsController@standardDetails','as'=>'osce.admin.SubjectStatisticsController.standardDetails']);
        //教学成绩分析
        Route::get('testscores/test-scores-count',['uses'=>'TestScoresController@testScoresCount','as'=>'osce.admin.TestScoresController.testScoresCount']);
        //教学成绩分析-获取科目
        Route::get('testscores/subject-lists',['uses'=>'TestScoresController@getSubjectLists','as'=>'osce.admin.TestScoresController.getSubjectLists']);
        //教学成绩分析-获取获取教室列表数据
        Route::get('testscores/teacher-data-list',['uses'=>'TestScoresController@getTeacherDataList','as'=>'osce.admin.TestScoresController.getTeacherDataList']);
        //教学成绩分析-班级历史成绩
        Route::get('testscores/grade-score-list',['uses'=>'TestScoresController@getGradeScoreList','as'=>'osce.admin.TestScoresController.getGradeScoreList']);
        //教学成绩分析-班级成绩明细
        Route::get('testscores/grade-detail',['uses'=>'TestScoresController@getGradeDetail','as'=>'osce.admin.TestScoresController.getGradeDetail']);
            //题库考核表签(yang)
         Route::get('exam/exam-label', ['uses'=>'ExamLabelController@getExamLabel','as'=>'osce.admin.ExamLabelController.getExamLabel']);


            //时间管理

            Route::get('examinationpaper/exam-list',['uses'=>'ExaminationPaperController@getExamList','as'=>'osce.admin.ExaminationPaperController.getExamList']);

    });

});