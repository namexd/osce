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
        Route::get('exam/exam-getlabel', ['uses'=>'ExamLabelController@getEditExamQuestionLabel','as'=>'osce.admin.ExamLabelController.getEditExamQuestionLabel']);
        //编辑试卷标签内容
        Route::post('exam/exam-editlabel', ['uses'=>'ExamLabelController@editExamQuestionLabelInsert','as'=>'osce.admin.ExamLabelController.editExamQuestionLabelInsert']);
        //删除试卷标签
        Route::get('exam/exam-deletelabel', ['uses'=>'ExamLabelController@getDeleteExamQuestionLabel','as'=>'osce.admin.ExamLabelController.getDeleteExamQuestionLabel']);
        //新增试卷标签
        Route::get('exam/exam-addlabel', ['uses'=>'ExamLabelController@addExamQuestionLabel','as'=>'osce.admin.ExamLabelController.addExamQuestionLabel']);
        Route::post('exam/exam-addlabel', ['uses'=>'ExamLabelController@postAddExamQuestionLabel','as'=>'osce.admin.ExamLabelController.postAddExamQuestionLabel']);
        //新增试卷编辑验证标签
        Route::get('exam/exam-addverify', ['uses'=>'ExamLabelController@examAddLabelVerify','as'=>'osce.admin.ExamLabelController.examAddLabelVerify']);
        //编辑试卷标签验证
        Route::get('exam/exam-editverify', ['uses'=>'ExamLabelController@examEditLabelVerify','as'=>'osce.admin.ExamLabelController.examEditLabelVerify']);
        //答卷查询
        Route::get('examanswer/student-answer/{student_id}', ['uses'=>'ExamAnswerController@getStudentAnswer','as'=>'osce.admin.ExamAnswerController.getStudentAnswer']);

        //试卷标签验证
        Route::post('exam/exam-verify', ['uses'=>'ExamLabelController@postCheckNameOnly','as'=>'osce.admin.ExamLabelController.postCheckNameOnly']);



        //试卷管理-列表
        Route::get('exampaper/exam-list',['uses'=>'ExamPaperController@getExamList','as'=>'osce.admin.ExamPaperController.getExamList']);
        //试卷管理-ajax获取抽题范围
        Route::get('exampaper/question-round',['uses'=>'ExamPaperController@getQuestionRound','as'=>'osce.admin.ExamPaperController.getQuestionRound']);
        //试卷管理-删除试卷
        Route::get('exampaper/delete-exam',['uses'=>'ExamPaperController@getDeleteExam','as'=>'osce.admin.ExamPaperController.getDeleteExam']);
        //试卷管理-新增试卷
        Route::get('exampaper/add-exam-page',['uses'=>'ExamPaperController@getAddExamPage','as'=>'osce.admin.ExamPaperController.getAddExamPage']);
        //
        Route::get('exampaper/scope-callback',['uses'=>'ExamPaperController@scopeCallback','as'=>'osce.admin.ExamPaperController.scopeCallback']);

        ////试卷管理-获取试题标签
        Route::get('exampaper/examp-questions',['uses'=>'ExamPaperController@getExampQuestions','as'=>'osce.admin.ExamPaperController.getExampQuestions']);
        //试卷管理-获取试卷试题数据
        Route::get('exampaper/exam-questions',['uses'=>'ExamPaperController@getExamQuestions','as'=>'osce.admin.ExamPaperController.getExamQuestions']);
        //试卷管理-新增试卷操作
        Route::post('exampaper/add-exams',['uses'=>'ExamPaperController@getAddExams','as'=>'osce.admin.ExamPaperController.getAddExams']);
        //试卷管理-修改试卷
        Route::post('exampaper/edit-exam-paper',['uses'=>'ExamPaperController@getEditExamPaper','as'=>'osce.admin.ExamPaperController.getEditExamPaper']);
        //题库管理列表
        Route::get('examquestion/examquestion-list',['uses'=>'ExamQuestionController@showExamQuestionList','as'=>'osce.admin.ExamQuestionController.showExamQuestionList']);
        //试卷管理-验证试卷
        Route::post('exampaper/check-name-only',['uses'=>'ExamPaperController@postCheckNameOnly','as'=>'osce.admin.ExamPaperController.postCheckNameOnly']);

        //试卷管理-验证试卷题目数量
        Route::post('exampaper/check-questions-num',['uses'=>'ExamPaperController@postCheckQuestionsNum','as'=>'osce.admin.ExamPaperController.postCheckQuestionsNum']);


        //题库管理新增
        Route::get('examquestion/examquestion-add',['uses'=>'ExamQuestionController@getExamQuestionAdd','as'=>'osce.admin.ExamQuestionController.getExamQuestionAdd']);//新增页面
        Route::post('examquestion/examquestion-add',['uses'=>'ExamQuestionController@postExamQuestionAdd','as'=>'osce.admin.ExamQuestionController.postExamQuestionAdd']);//新增数据交互
        //题库图片上传
        Route::post('examquestion/examquestion-upload',['uses'=>'ExamQuestionController@postQuestionUpload','as'=>'osce.admin.ExamQuestionController.postQuestionUpload']);

        //题库管理编辑
        Route::get('examquestion/examquestion-edit/{id}',['uses'=>'ExamQuestionController@getExamQuestionEdit','as'=>'osce.admin.ExamQuestionController.getExamQuestionEdit']);//编辑页面
        Route::post('examquestion/examquestion-edit',['uses'=>'ExamQuestionController@postExamQuestionEdit','as'=>'osce.admin.ExamQuestionController.postExamQuestionEdit']);//保存编辑

        //题库管理删除
        Route::post('examquestion/examquestion-delete',['uses'=>'ExamQuestionController@examQuestionDelete','as'=>'osce.admin.ExamQuestionController.examQuestionDelete']);



        //理论考试，答题时，试卷信息
        Route::get('answer/formalpaper-list',['uses'=>'AnswerController@formalPaperList','as'=>'osce.admin.AnswerController.formalPaperList']);
        //保存考生答案
        Route::post('answer/postsaveanswer',['uses'=>'AnswerController@postSaveAnswer','as'=>'osce.admin.AnswerController.postSaveAnswer']);

        //查询理论考试考试是否结束
        Route::post('answer/isfinish',['uses'=>'AnswerController@isfinish','as'=>'osce.admin.AnswerController.isfinish']);

        //查询该该考生理论考试的成绩
        Route::get('answer/selectgrade',['uses'=>'AnswerController@selectGrade','as'=>'osce.admin.AnswerController.selectGrade']);



        //展示编辑子项页面
        Route::get('api/editor-exam-paper-item',['uses'=>'ApiController@GetEditorExamPaperItem','as'=>'osce.admin.ApiController.GetEditorExamPaperItem']);
        //处理编辑子项数据
        Route::post('api/editor-exam-paper-item',['uses'=>'ApiController@PostEditorExamPaperItem','as'=>'osce.admin.ApiController.PostEditorExamPaperItem']);
        //预览试卷控制器
        Route::get('api/exam-paper-preview',['uses'=>'ApiController@ExamPaperPreview','as'=>'osce.admin.ApiController.ExamPaperPreview']);
        //生成试卷的方法
        Route::get('api/generate-exam-paper',['uses'=>'ApiController@GenerateExamPaper','as'=>'osce.admin.ApiController.GenerateExamPaper']);

        /************理论考试begin*************/
        //理论考试登录界面
        Route::get('api/loginauth-view',['uses'=>'ApiController@LoginAuthView','as'=>'osce.admin.ApiController.LoginAuthView','middleware'=>['teacher-guest']]);

        //理论考试登录数据交互
        Route::post('api/loginauth-info',['uses'=>'ApiController@LoginAuth','as'=>'osce.admin.ApiController.LoginAuthInfo']);

        //监考老师登录成功页面
        Route::get('api/loginauth-wait',['uses'=>'ApiController@LoginAuthWait','as'=>'osce.admin.ApiController.LoginAuthWait']);

        //考生登录成功页面
        Route::get('api/student-exam-index',['uses'=>'ApiController@getStudentExamIndex','as'=>'osce.admin.ApiController.getStudentExamIndex']);

        //获取考试id
        Route::get('api/get-exampaperid',['uses'=>'ApiController@getExamPaperId','as'=>'osce.admin.ApiController.getExamPaperId']);

        //获取当前考站所在流程考试是否已经结束
        Route::get('api/exam-paper-status',['uses'=>'ApiController@getExamPaperStatus','as'=>'osce.admin.ApiController.getExamPaperStatus']);

        //理论考试登录页面地址
        //Route::get('api/examinee-info',['uses'=>'ApiController@ExamineeInfo','as'=>'osce.admin.ApiController.ExamineeInfo']);

        //学生等待进入考试页面
        Route::get('api/wait-examing',['uses'=>'ApiController@getWaitExaming','as'=>'osce.admin.ApiController.getWaitExaming']);
        /************理论考试end*************/

        /*****************考试监控begin*******************/
        //正在考试
        Route::get('exam-monitor/normal',['uses'=>'ExamMonitorController@getExamMonitorNormalList','as'=>'osce.admin.ExamMonitorController.getExamMonitorNormalList']);
        //迟到
        Route::get('exam-monitor/late',['uses'=>'ExamMonitorController@getExamMonitorLateList','as'=>'osce.admin.ExamMonitorController.getExamMonitorLateList']);
        //替考
        Route::get('exam-monitor/replace',['uses'=>'ExamMonitorController@getExamMonitorReplaceList','as'=>'osce.admin.ExamMonitorController.getExamMonitorReplaceList']);
        //弃考
        Route::get('exam-monitor/quit',['uses'=>'ExamMonitorController@getExamMonitorQuitList','as'=>'osce.admin.ExamMonitorController.getExamMonitorQuitList']);
        //已完成
        Route::get('exam-monitor/finish',['uses'=>'ExamMonitorController@getExamMonitorFinishList','as'=>'osce.admin.ExamMonitorController.getExamMonitorFinishList']);
        //视频指向
        Route::get('exam-monitor/video',['uses'=>'ExamMonitorController@getExamMonitorHeadInfo','as'=>'osce.admin.ExamMonitorController.getExamMonitorHeadInfo']);
        /*****************考试监控end*******************/


        /****************android c++接口begin*****************/
        // android端教师点击 准备完成
        Route::get('api/ready-exam',['uses'=>'ApiController@getReadyExam','as'=>'osce.admin.ApiController.getReadyExam']);
        // android端替考警告
        Route::post('api/replace-exam-alert',['uses'=>'ApiController@postAlertExamReplace','as'=>'osce.admin.ApiController.postAlertExamReplace']);
        /****************android c++接口end*****************/


        //获取正在考试列表
        Route::get('exam-control/getexamlist',['uses'=>'ExamControlController@getExamlist','as'=>'osce.admin.ExamControlController.getExamlist']);
        //终止考试数据交互
        Route::get('exam-control/poststopexam',['uses'=>'ExamControlController@postStopExam','as'=>'osce.admin.ExamControlController.postStopExam']);

        //获取正在考试的视频
        Route::get('exam-control/getvcrslist',['uses'=>'ExamControlController@getVcrsList','as'=>'osce.admin.ExamControlController.getVcrsList']);




    });

});

