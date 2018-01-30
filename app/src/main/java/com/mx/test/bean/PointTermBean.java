package com.mx.test.bean;

import android.os.Parcel;
import android.os.Parcelable;

/**
 * 考核点的子评分项
 * 
 * @author DELL
 *
 */
public class PointTermBean implements Parcelable {

	private String id;//
	private String subject_id;//
	private String content;
	private String sort;//
	private String score;//
	private String real;

	private String pid;//
	private String level;
	private String created_user_id;
	private String created_at;
	private String updated_at;
	private String answer;

	private boolean isScored;
	// 考项的第一次打分时间
	private String scoreTime;

	private int color = 0;

	public int getColor() {
		return color;
	}

	public void setColor(int color) {
		this.color = color;
	}

	public String getScoreTime() {
		return scoreTime;
	}

	public void setScoreTime(String scoreTime) {
		this.scoreTime = scoreTime;
	}

	public boolean isScored() {
		return isScored;
	}

	public void setScored(boolean isScored) {
		this.isScored = isScored;
	}

	public String getReal() {
		return real;
	}

	public void setReal(String real) {
		this.real = real;
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

	public String getPid() {
		return pid;
	}

	public void setPid(String pid) {
		this.pid = pid;
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

	public String getAnswer() {
		return answer;
	}

	public void setAnswer(String answer) {
		this.answer = answer;
	}

	public static final Parcelable.Creator<PointTermBean> CREATOR = new Creator<PointTermBean>() {

		@Override
		public PointTermBean createFromParcel(Parcel source) {

			PointTermBean term = new PointTermBean();
			term.id = source.readString();
			term.subject_id = source.readString();
			term.content = source.readString();
			term.sort = source.readString();
			term.score = source.readString();
			term.real = source.readString();
			term.pid = source.readString();
			term.level = source.readString();
			term.created_user_id = source.readString();
			term.created_at = source.readString();
			term.updated_at = source.readString();
			term.answer = source.readString();

			return term;
		}

		@Override
		public PointTermBean[] newArray(int size) {
			return new PointTermBean[size];
		}
	};

	@Override
	public int describeContents() {
		return 0;
	}

	@Override
	public void writeToParcel(Parcel dest, int flags) {

		dest.writeString(id);
		dest.writeString(subject_id);
		dest.writeString(content);
		dest.writeString(sort);
		dest.writeString(score);
		dest.writeString(real);
		dest.writeString(pid);
		dest.writeString(level);
		dest.writeString(created_user_id);
		dest.writeString(created_at);
		dest.writeString(updated_at);
		dest.writeString(answer);

	}

}