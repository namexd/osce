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



        //题库考核表签(cy)
        Route::get('exam/exam-label', ['uses'=>'ExamLabelController@getExamLabel','as'=>'osce.admin.ExamLabelController.getExamLabel']);
        //获取编辑试卷标签内容
        Route::get('exam/exam-getLabel', ['uses'=>'ExamLabelController@getEditExamQuestionLabel','as'=>'osce.admin.ExamLabelController.getEditExamQuestionLabel']);
        //编辑试卷标签内容
        Route::get('exam/exam-editLabel', ['uses'=>'ExamLabelController@editExamQuestionLabelInsert','as'=>'osce.admin.ExamLabelController.editExamQuestionLabelInsert']);
        //删除试卷标签
        Route::get('exam/exam-deleteLabel', ['uses'=>'ExamLabelController@getDeleteExamQuestionLabel','as'=>'osce.admin.ExamLabelController.getDeleteExamQuestionLabel']);
        //新增试卷标签
        Route::get('exam/exam-addLabel', ['uses'=>'ExamLabelController@addExamQuestionLabel','as'=>'osce.admin.ExamLabelController.addExamQuestionLabel']);
        Route::post('exam/exam-addLabel', ['uses'=>'ExamLabelController@postAddExamQuestionLabel','as'=>'osce.admin.ExamLabelController.postAddExamQuestionLabel']);



        //试卷管理-列表
        Route::get('exampaper/exam-list',['uses'=>'ExamPaperController@getExamList','as'=>'osce.admin.ExamPaperController.getExamList']);
        //试卷管理-ajax获取抽题范围
        Route::get('exampaper/question-round',['uses'=>'ExamPaperController@getQuestionRound','as'=>'osce.admin.ExamPaperController.getQuestionRound']);
        //试卷管理-删除试卷
        Route::get('exampaper/delete-exam',['uses'=>'ExamPaperController@getDeleteExam','as'=>'osce.admin.ExamPaperController.getDeleteExam']);
        //试卷管理-新增试卷
        Route::get('exampaper/add-exam-page',['uses'=>'ExamPaperController@getAddExamPage','as'=>'osce.admin.ExamPaperController.getAddExamPage']);

        //题库管理新增
        Route::get('examQuestion/examQuestion-add',['uses'=>'ExamQuestionController@getExamQuestionAdd','as'=>'osce.admin.ExamQuestionController.getExamQuestionAdd']);//新增页面
        Route::post('examQuestion/examQuestion-add',['uses'=>'ExamQuestionController@postExamQuestionAdd','as'=>'osce.admin.ExamQuestionController.postExamQuestionAdd']);//新增数据交互

        //题库管理编辑
        Route::get('examQuestion/examQuestion-edit',['uses'=>'ExamQuestionController@getExamQuestionEdit','as'=>'osce.admin.ExamQuestionController.getExamQuestionEdit']);//编辑页面
        Route::post('examQuestion/examQuestion-edit',['uses'=>'ExamQuestionController@postExamQuestionEdit','as'=>'osce.admin.ExamQuestionController.postExamQuestionEdit']);//保存编辑

        //题库管理删除
        Route::post('examQuestion/examQuestion-delete',['uses'=>'ExamQuestionController@examQuestionDelete','as'=>'osce.admin.ExamQuestionController.examQuestionDelete']);

    });

});