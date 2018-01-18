package com.mx.osce.util;

public class Constant {

	public static final String SHOW_REFRESH = "refresh";

	public static final String NORMAL_TAG = "normal";

	public static final String SPECIAL_TAG = "special";

	/** 考站排序 */
	public static final String STATION = "2";

	/** 考场排序 */
	public static final String EXAMINATION = "1";

	/** 理论考试类型 */
	public static final String THEORY_STATION = "3";

	/** SP考站 */
	public static final String SP_STATION = "2";
	/** SP考试老师类型 */
	public static final String TEACHER_TYPE_SP = "2";

	/** 技能考站 */
	public static final String SKILL_STATION = "1";

	/** 订阅强制下线码 */
	public static final int CODE_OFF_LINE = 100;

	// /** 订阅强制终止码 */
	// public static final int CODE_FOREC_END = 101;

	/** 订阅放弃考试码 */
	public static final int CODE_GIVE_UP_EXAM = 107;

	/** 订阅PC端理论考试学生提交成绩码 */
	public static final int CODE_THRORY_EXAM_BY_PC_END = 108;

	/** 订阅当前考生码 */
	public static final int CODE_CURRENT_STUDENT = 102;

	/** 订阅当前组考生码 */
	public static final int CODE_CURRENT_GROUP = 103;

	/** 订阅下一组考生码 */
	public static final int CODE_NEXT_GROUP = 104;

	/** 订阅开始考试码 */
	public static final int CODE_BEGIN_EXAM = 105;

	/** 订阅结束考试码 */
	public static final int CODE_WARN_END_EXAM = 106;

	/** 照相机 */
	public static final int CAMERA_REQUEST = 1;

	/** 录音 */
	public static final int MIC_REQUEST = 2;

	/** 开启Nfc */
	public static final String ACTION_START_NFC_READER = "com.mx.osce.broadcast.MainActivity.NFC_Start";

	/** 警告后确认结束考试-Action */
	public static final String ACTION_WARN_EXAM_END = "com.mx.osce.broadcast.Force_Warn_End_Exam";

	/** 强制下线 -Action */
	public static final String ACTION_FORCE_OFFINE = "com.mx.osce.broadcast.Force_Off_Line";

	/** 改变当前小组-Action */
	public static final String ACTION_CHANGE_CURRENT_GROUP = "com.mx.osce.broadcast.MainActivity.Current_Group";

	/** 改变下一小组-Action */
	public static final String ACTION_CHANGE_NEXT_GROUP = "com.mx.osce.broadcast.MainActivity.Next_Group";

	/** 改变当前考生-Action */
	public static final String ACTION_CHANGE_CURRENT_STUDENT = "com.mx.osce.broadcast.MainActivity.Current_Student";

	/** PC理论开始考试-Action */
	public static final String ACTION_THEORY_EXAM_BEGIN = "com.mx.osce.broadcast.Force_Begin_Theory_Exam";

	/** PC理论结束考试 */
	public static final String ACTION_THEORY_EXAM_END = "com.mc.osce.broadcast.Force_End_Theory_Exam";

	/** 當前考生放弃考试 */
	public static final String ACTION_GIVE_UP_EXAM = "com.mx.osce.broadcast.Forece_Give_Up_Exam";

	public static final String ACTION_REFRESH = "refresh";

	/** 考试环境 */
	public static final String BasciUrl = "http://v3101.php56.dev.atapp.com";

	/** 登陆接口 */
	public static final String LOGIN = "/api/1.0/public/oauth/access_token";

	/** 当前考生，需要参数，腕表的IMEI */
	public static final String CURRENT_STUDENT = "/osce/api/invigilatepad/authentication";

	/** 获取当前考试小组的组员信息,user_id */
	public static final String CURRENT_GROUP = "/osce/pad/examinee";

	/** 获取下一考试小组的组员信息 ，user_id */
	public static final String NEXT_GROUP = "/osce/pad/next-examinee";

	/** 获取考试考核点，需要参数：考站id:station_id */
	public static final String GRADE_OPINT = "/osce/api/invigilatepad/exam-grade";

	/** 学生开始考试 时间,需要参数student_id，station_id */
	public static final String BEGIN_EXAM = "/osce/api/invigilatepad/start-exam";

	/** 抽签,需要参数：腕表的编号uid，房间的编号room_id */
	public static final String DRAW = "/osce/pad/station";

	/** 修改考生考试状态,最后一次评分，参数：考生id */
	public static final String CHANGE_STATUS = "/osce/pad/change-status";

	/** 得到考站相关信息，老师id */
	public static final String GET_STATIONID = "/osce/pad/station-list";

	/** 当前考生信息,参数station_id */
	public static final String POLLING = "/osce/api/invigilatepad/authentication";

	/** 成绩上传 */
	public static final String UPLOAD_SCORE = "/osce/api/invigilatepad/save-exam-result";

	/** 图片上传 */
	public static final String UOLOAD_IMAGE = "/osce/api/upload-image";

	/** 录音上传 */
	public static final String UPLOAD_AUDIO = "/osce/api/upload-radio";

	/** 时间描点上传 */
	public static final String UPLOAD_TIMES = "/osce/api/store-anchor";

	/** 获取摄像机 */
	public static final String GET_VIDEO = "/osce/pad/teacher-vcr";

	/** 准备完成 */
	public static final String READ_COMPLETE = "/osce/admin/api/ready-exam";

	/** 准备完成后，发出推送消息给腕表的请求 */
	public static final String PUBLISH_MESSAGE_TO_WATCH = "/osce/api/student-watch/student-exam-reminder";

	/** 标记替考警告 */
	public static final String WARN_EXAM = "/osce/admin/api/replace-exam-alert";

	/** 请求下一个 */
	public static final String NEXT_STUDENT = "/osce/pad/next-student";

	/** 获取考试剩余时间 */
	public static final String GET_TIME = "/osce/admin/exam-control/getTime";

	/** 刷新当前考生 */
	public static final String REFRESH = "/osce/pad/push-student";
	/** 提交异常考生 */
	public static final String End_AbnormalStudent_EXAM = "/osce/pad/dispose-aberrant-exam";

}
