package com.mx.osce.bean;

/** 当前考生小组信息 */
public class CurrentGroupStudentBean {
	/***************************************************************************************************************************************/

	// private String student_id;
	//
	// private String student_name;
	//
	// private String student_user_id;
	//
	// private String student_idcard;
	//
	// private String student_mobile;
	//
	// private String student_code;
	//
	// private String student_avator;
	//
	// private String student_description;
	/***************************************************************************************************************************************/

	private String student_name;

	private String student_code;

	private int student_user_id;

	private String student_idcard;

	private String student_mobile;

	private String student_avator;

	private int status;

	private int student_id;

	private String exam_sequence;

	private int exam_queue_id;

	private String student_description;

	private String station_id;
	
	private String room_id;
	

	public String getRoom_id() {
		return room_id;
	}

	public void setRoom_id(String room_id) {
		this.room_id = room_id;
	}



	public String getStation_id() {
		return station_id;
	}

	public void setStation_id(String station_id) {
		this.station_id = station_id;
	}

	public int getStatus() {
		return status;
	}

	public void setStatus(int status) {
		this.status = status;
	}

	public String getExam_sequence() {
		return exam_sequence;
	}

	public void setExam_sequence(String exam_sequence) {
		this.exam_sequence = exam_sequence;
	}

	public int getExam_queue_id() {
		return exam_queue_id;
	}

	public void setExam_queue_id(int exam_queue_id) {
		this.exam_queue_id = exam_queue_id;
	}

	public void setStudent_user_id(int student_user_id) {
		this.student_user_id = student_user_id;
	}

	public void setStudent_id(int student_id) {
		this.student_id = student_id;
	}

	public String getStudent_name() {
		return student_name;
	}

	public void setStudent_name(String student_name) {
		this.student_name = student_name;
	}

	public String getStudent_code() {
		return student_code;
	}

	public void setStudent_code(String student_code) {
		this.student_code = student_code;
	}

	public String getStudent_idcard() {
		return student_idcard;
	}

	public void setStudent_idcard(String student_idcard) {
		this.student_idcard = student_idcard;
	}

	public String getStudent_mobile() {
		return student_mobile;
	}

	public void setStudent_mobile(String student_mobile) {
		this.student_mobile = student_mobile;
	}

	public String getStudent_avator() {
		return student_avator;
	}

	public void setStudent_avator(String student_avator) {
		this.student_avator = student_avator;
	}

	public String getStudent_description() {
		return student_description;
	}

	public void setStudent_description(String student_description) {
		this.student_description = student_description;
	}

	public int getStudent_user_id() {
		return student_user_id;
	}

	public int getStudent_id() {
		return student_id;
	}

}
