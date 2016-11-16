package com.mx.osce.bean;

public class PollingStudentBean {
	/****************************************************************************************************************************/
	// private String name;
	//
	// private String code;
	//
	// private String idcard;
	//
	// private String mobile;
	//
	// private String avator;
	//
	// private String status;
	//
	// private String student_id;
	//
	// private String exam_sequence;

	/****************************************************************************************************************************/
	private String name;

	private String code;

	private String idcard;

	private String mobile;

	private String avator;

	private int status;

	private int student_id;

	private String exam_sequence;

	private int teacher_id;

	private int exam_queue_id;
	//2016-6-17异常考生时添加
	private String controlMark ;
	private String reason ;

 private String station_id;

	 public String getStation_id() {
	 return station_id;
	 }
	
	 public String getControlMark() {
		return controlMark;
	}

	public void setControlMark(String controlMark) {
		this.controlMark = controlMark;
	}

	public String getReason() {
		return reason;
	}

	public void setReason(String reason) {
		this.reason = reason;
	}

	public void setStation_id(String station_id) {
	 this.station_id = station_id;
	 }

	public String getName() {
		return name;
	}

	public void setName(String name) {
		this.name = name;
	}

	public String getCode() {
		return code;
	}

	public void setCode(String code) {
		this.code = code;
	}

	public String getIdcard() {
		return idcard;
	}

	public void setIdcard(String idcard) {
		this.idcard = idcard;
	}

	public String getMobile() {
		return mobile;
	}

	public void setMobile(String mobile) {
		this.mobile = mobile;
	}

	public String getAvator() {
		return avator;
	}

	public void setAvator(String avator) {
		this.avator = avator;
	}

	public int getStatus() {
		return status;
	}

	public void setStatus(int status) {
		this.status = status;
	}

	public int getStudent_id() {
		return student_id;
	}

	public void setStudent_id(int student_id) {
		this.student_id = student_id;
	}

	public String getExam_sequence() {
		return exam_sequence;
	}

	public void setExam_sequence(String exam_sequence) {
		this.exam_sequence = exam_sequence;
	}

	public int getTeacher_id() {
		return teacher_id;
	}

	public void setTeacher_id(int teacher_id) {
		this.teacher_id = teacher_id;
	}

	public int getExam_queue_id() {
		return exam_queue_id;
	}

	public void setExam_queue_id(int exam_queue_id) {
		this.exam_queue_id = exam_queue_id;
	}

}
