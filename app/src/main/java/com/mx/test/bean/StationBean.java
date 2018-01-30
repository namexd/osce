package com.mx.test.bean;

/**
 * 考站
 * 
 * @author DELL
 *
 */
public class StationBean {

	// "code": null,
	// "type": "1",
	// "description": null,
	// "subject_id": "34",
	// "mins": "0",
	// "paper_id": null,
	// "create_user_id": "0",
	// "archived": "0",
	// "created_at": "2016-04-20 10:12:57",
	// "updated_at": "2016-04-20 10:12:57",
	// "exam_screening_id": "143",
	// "room_id": "16",
	// "exam_id": "4",
	// "sequence_mode": "1",
	// "station_type": "1"

	private String id;

	private String name;

	private String type;

	private String subject_id;

	private String mins;

	private String create_user_id;

	private String archived;

	private String created_at;

	private String updated_at;

	private String exam_screening_id;

	private String room_id;

	private String exam_id;

	private String sequence_mode;

	private long service_time;

	private String station_type;

	private String teacher_type;// type=2,SP---teacher;

	public String getTeacher_type() {
		return teacher_type;
	}

	public void setTeacher_type(String teacher_type) {
		this.teacher_type = teacher_type;
	}

	public String getId() {
		return id;
	}

	public void setId(String id) {
		this.id = id;
	}

	public String getName() {
		return name;
	}

	public void setName(String name) {
		this.name = name;
	}

	public String getType() {
		return type;
	}

	public void setType(String type) {
		this.type = type;
	}

	public String getSubject_id() {
		return subject_id;
	}

	public void setSubject_id(String subject_id) {
		this.subject_id = subject_id;
	}

	public String getMins() {
		return mins;
	}

	public void setMins(String mins) {
		this.mins = mins;
	}

	public String getCreate_user_id() {
		return create_user_id;
	}

	public void setCreate_user_id(String create_user_id) {
		this.create_user_id = create_user_id;
	}

	public String getArchived() {
		return archived;
	}

	public void setArchived(String archived) {
		this.archived = archived;
	}

	public String getCreated_at() {
		return created_at;
	}

	public void setCreated_at(String created_at) {
		this.created_at = created_at;
	}

	public String getUpdated_at() {
		return updated_at;
	}

	public void setUpdated_at(String updated_at) {
		this.updated_at = updated_at;
	}

	public String getExam_screening_id() {
		return exam_screening_id;
	}

	public void setExam_screening_id(String exam_screening_id) {
		this.exam_screening_id = exam_screening_id;
	}

	public String getRoom_id() {
		return room_id;
	}

	public void setRoom_id(String room_id) {
		this.room_id = room_id;
	}

	public String getExam_id() {
		return exam_id;
	}

	public void setExam_id(String exam_id) {
		this.exam_id = exam_id;
	}

	public String getSequence_mode() {
		return sequence_mode;
	}

	public void setSequence_mode(String sequence_mode) {
		this.sequence_mode = sequence_mode;
	}

	public long getService_time() {
		return service_time;
	}

	public void setService_time(long service_time) {
		this.service_time = service_time;
	}

	public String getStation_type() {
		return station_type;
	}

	public void setStation_type(String station_type) {
		this.station_type = station_type;
	}

}
