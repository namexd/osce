package com.mx.test.bean;

import java.util.ArrayList;

import android.os.Parcel;
import android.os.Parcelable;

public class NormalPoint implements Parcelable {

	/** 考项的id */
	private String id;
	/** 考项的subject_id */
	private String subject_id;
	/** 考项的内容 */
	private String content;
	/** 考项的sort */
	private String sort;
	/** 考项的score */
	private String score;
	/** 考项的pid */
	private String pid;
	/** 考项的level */
	private String level;
	/** 考项的created_user_id */
	private String created_user_id;
	/** 考项的created_at */
	private String created_at;
	/** 考项的updated_at */
	private String updated_at;
	/** 考项的注意事项点 */
	private String answer;
	/** 单个考项的多个考点 */
	public ArrayList<PointTermBean> test_term;

	public NormalPoint(Parcel source) {
		id = source.readString();
		subject_id = source.readString();
		content = source.readString();
		sort = source.readString();
		score = source.readString();
		pid = source.readString();
		level = source.readString();
		created_user_id = source.readString();
		created_at = source.readString();
		updated_at = source.readString();
		answer = source.readString();
		test_term = new ArrayList<PointTermBean>();
		// readTypedList方法返回为void ,所以需要实例化一个对象
		source.readTypedList(test_term, PointTermBean.CREATOR);

	}

	public NormalPoint() {
	}

	public String getId() {
		return id;
	}

	public void setId(String id) {
		this.id = id;
	}

	public String getSubject_id() {
		return subject_id;
	}

	public void setSubject_id(String subject_id) {
		this.subject_id = subject_id;
	}

	public String getContent() {
		return content;
	}

	public void setContent(String content) {
		this.content = content;
	}

	public String getSort() {
		return sort;
	}

	public void setSort(String sort) {
		this.sort = sort;
	}

	public String getScore() {
		return score;
	}

	public void setScore(String score) {
		this.score = score;
	}

	public String getLevel() {
		return level;
	}

	public void setLevel(String level) {
		this.level = level;
	}

	public String getCreated_user_id() {
		return created_user_id;
	}

	public void setCreated_user_id(String created_user_id) {
		this.created_user_id = created_user_id;
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

	public String getPid() {
		return pid;
	}

	public void setPid(String pid) {
		this.pid = pid;
	}

	public String getAnswer() {
		return answer;
	}

	public void setAnswer(String answer) {
		this.answer = answer;
	}

	public ArrayList<PointTermBean> getTest_term() {
		return test_term;
	}

	public void setTest_term(ArrayList<PointTermBean> test_term) {
		this.test_term = test_term;
	}

	public static final Parcelable.Creator<GradePointBean_Net> CREATOR = new Creator<GradePointBean_Net>() {

		@Override
		public GradePointBean_Net createFromParcel(Parcel source) {

			return new GradePointBean_Net(source);
		}

		@Override
		public GradePointBean_Net[] newArray(int size) {
			return new GradePointBean_Net[size];
		}

	};

	@Override
	public int describeContents() {
		return 0;
	}

	@Override
	public void writeToParcel(Parcel dest, int flags) {

		dest.writeString("id");
		dest.writeString("subject_id");
		dest.writeString("content");
		dest.writeString("sort");
		dest.writeString("score");
		dest.writeString("pid");
		dest.writeString("level");
		dest.writeString("created_user_id");
		dest.writeString("created_at");
		dest.writeString("updated_at");
		dest.writeTypedList(test_term);

	}

}
