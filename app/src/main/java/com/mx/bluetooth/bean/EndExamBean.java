package com.mx.bluetooth.bean;

public class EndExamBean {

	private int code;

	private String message;

	private EndStatu data;

	public int getCode() {
		return code;
	}

	public void setCode(int code) {
		this.code = code;
	}

	public String getMessage() {
		return message;
	}

	public void setMessage(String message) {
		this.message = message;
	}

	public EndStatu getData() {
		return data;
	}

	public void setData(EndStatu data) {
		this.data = data;
	}

	public class EndStatu {

		private String student_id;

		private String end_time;

	private String exam_screening_id;

		public String getStudent_id() {
			return student_id;
		}

		public void setStudent_id(String student_id) {
			this.student_id = student_id;
		}

		public String getEnd_time() {
			return end_time;
		}

		public void setEnd_time(String end_time) {
			this.end_time = end_time;
		}

		public String getExam_screening_id() {
			return exam_screening_id;
		}

		public void setExam_screening_id(String exam_screening_id) {
			this.exam_screening_id = exam_screening_id;
		}
	}
}
