package com.mx.osce.bean;

public class StartExamBean {

	private int code;

	private String message;

	private StartExam data;

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

	public StartExam getData() {
		return data;
	}

	public void setData(StartExam data) {
		this.data = data;
	}

	public class StartExam {

		private String student_id;

		private String start_time;

		private String exam_screening_id;

		public String getStudent_id() {
			return student_id;
		}

		public void setStudent_id(String student_id) {
			this.student_id = student_id;
		}

		public String getStart_time() {
			return start_time;
		}

		public void setStart_time(String start_time) {
			this.start_time = start_time;
		}

		public String getExam_screening_id() {
			return exam_screening_id;
		}

		public void setExam_screening_id(String exam_screening_id) {
			this.exam_screening_id = exam_screening_id;
		}
	}

}
