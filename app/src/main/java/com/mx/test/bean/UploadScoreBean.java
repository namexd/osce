package com.mx.test.bean;

public class UploadScoreBean {
	private String student_id;

	private String station_id;
	// 评分老师的id
			private String teacher_id;
	// 点击考试时间
	private String begin_dt;
	
	// 点击前去评价，网络请求返回的时间戳
	private String end_dt;
	// 评价内容
	private String evaluate;
	// 操作的连贯性
	private String operation;
	// 工作的娴熟度
	private String skilled;
	// 病人关怀情况
	private String patient;
	// 沟通亲和能力
	private String affinity;
	private String exam_screening_id;
	private String upload_image_return;
	private String score;
	private String url;
	// 上传状态
	private String states;//1为以上传，0为未上传，2为正在上传,3为等待上传（在上传队列中）
	
	
	public String getExam_screening_id() {
		return exam_screening_id;
	}
	public void setExam_screening_id(String exam_screening_id) {
		this.exam_screening_id = exam_screening_id;
	}
	public String getUpload_image_return() {
		return upload_image_return;
	}
	public void setUpload_image_return(String upload_image_return) {
		this.upload_image_return = upload_image_return;
	}
	public String getScore() {
		return score;
	}
	public void setScore(String score) {
		this.score = score;
	}
	
	public String getStudent_id() {
		return student_id;
	}
	public void setStudent_id(String student_id) {
		this.student_id = student_id;
	}
	public String getStation_id() {
		return station_id;
	}
	public void setStation_id(String station_id) {
		this.station_id = station_id;
	}
	public String getBegin_dt() {
		return begin_dt;
	}
	public void setBegin_dt(String begin_dt) {
		this.begin_dt = begin_dt;
	}
	public String getEnd_dt() {
		return end_dt;
	}
	public void setEnd_dt(String end_dt) {
		this.end_dt = end_dt;
	}
	public String getEvaluate() {
		return evaluate;
	}
	public void setEvaluate(String evaluate) {
		this.evaluate = evaluate;
	}
	public String getOperation() {
		return operation;
	}
	public void setOperation(String operation) {
		this.operation = operation;
	}
	public String getSkilled() {
		return skilled;
	}
	public void setSkilled(String skilled) {
		this.skilled = skilled;
	}
	public String getPatient() {
		return patient;
	}
	public void setPatient(String patient) {
		this.patient = patient;
	}
	public String getAffinity() {
		return affinity;
	}
	public void setAffinity(String affinity) {
		this.affinity = affinity;
	}
	public String getTeacher_id() {
		return teacher_id;
	}
	public void setTeacher_id(String teacher_id) {
		this.teacher_id = teacher_id;
	}
	public String getStates() {
		return states;
	}
	public void setStates(String states) {
		this.states = states;
	}
	public String getUrl() {
		return url;
	}
	public void setUrl(String url) {
		this.url = url;
	}
	
	
	
	
}
