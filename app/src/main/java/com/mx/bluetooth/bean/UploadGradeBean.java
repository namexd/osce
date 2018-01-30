package com.mx.bluetooth.bean;

import java.util.ArrayList;

/** 上传考生评分详情 */
public class UploadGradeBean {

	private String student_id;

	private String station_id;
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
	// 评分老师的id
	private String teacher_id;
	// 时间描点
	private String[] timeAnchors;

	private ArrayList<GradePointBean_Net> pointList;

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

	public String[] getTimeAnchors() {
		return timeAnchors;
	}

	public void setTimeAnchors(String[] timeAnchors) {
		this.timeAnchors = timeAnchors;
	}

	public ArrayList<GradePointBean_Net> getPointList() {
		return pointList;
	}

	public void setPointList(ArrayList<GradePointBean_Net> pointList) {
		this.pointList = pointList;
	}

}
