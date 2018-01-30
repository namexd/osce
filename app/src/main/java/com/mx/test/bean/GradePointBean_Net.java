package com.mx.test.bean;

import java.util.ArrayList;

import android.annotation.SuppressLint;
import android.os.Parcel;
import android.os.Parcelable;

/**
 * 评分的考项的实体类
 * 
 * @author DELL
 *
 */

@SuppressLint("ResourceAsColor")
public class GradePointBean_Net implements Parcelable {

	/** 1考项的id */
	private String id;
	/** 2考项的subject_id */
	private String subject_id;
	/** 3 考项的内容 */
	private String content;
	/** 4考项的sort */
	private String sort;
	/** 5考项的score */
	private String score;
	/** 6考项的pid */
	private String pid;
	/** 7考项的level */
	private String level;
	/** 8考项的created_user_id */
	private String created_user_id;
	/** 9考项的created_at */
	private String created_at;
	/** 10考项的updated_at */
	private String updated_at;
	/** 11考项的注意事项点 */
	private String answer;
	/** 12考点Tag */
	private String tag;
	/** 13特殊考点的标题 */
	private String title;
	/** 14单个考项的多个考点 */
	private ArrayList<PointTermBean> test_term;
	/** 15打分时间 */
	private String scoreTime;
	/** 16扣分 */
	private String subtract;

	public String getScoreTime() {
		return scoreTime;
	}

	public void setScoreTime(String scoreTime) {
		this.scoreTime = scoreTime;
	}

	public String getSubtract() {

		return subtract;
	}

	public void setSubtract(String subtract) {
		this.subtract = subtract;
	}

	public String getTitle() {
		return title;
	}

	public void setTitle(String title) {
		this.title = title;
	}

	public String getTag() {
		return tag;
	}

	public void setTag(String tag) {
		this.tag = tag;
	}

	public GradePointBean_Net(Parcel source) {
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
		tag = source.readString();
		title = source.readString();
		subtract = source.readString();
		test_term = new ArrayList<PointTermBean>();
		// readTypedList方法返回为void ,所以需要实例化一个对象
		source.readTypedList(test_term, PointTermBean.CREATOR);

	}

	public GradePointBean_Net() {
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
		dest.writeString("answer");
		dest.writeString("created_user_id");
		dest.writeString("created_at");
		dest.writeString("updated_at");
		dest.writeTypedList(test_term);
		dest.writeString("tag");
		dest.writeString("title");
		dest.writeString(subtract);

	}

	// public static GradePointBean_Local convert2Local(GradePointBean_Net
	// netData) {
	//
	// GradePointBean_Local localData = new GradePointBean_Local();
	//
	// localData.setAnswer(netData.getAnswer());
	// localData.setContent(netData.getContent());
	// localData.setCreated_at(netData.getCreated_at());
	// localData.setCreated_user_id(netData.getCreated_user_id());
	// localData.setId(netData.getId());
	// localData.setLevel(netData.getLevel());
	// localData.setPid(netData.getPid());
	// localData.setScore(netData.getScore());
	// localData.setSort(netData.getSort());
	// localData.setSubject_id(netData.getSubject_id());
	// localData.setTest_term(netData.getTest_term());
	// localData.setUpdated_at(netData.getUpdated_at());
	// localData.setSubject_id(netData.getSubject_id());
	// localData.setTag(netData.getTag());
	// localData.setTitle(netData.getTitle());
	// localData.setRealSubtract(netData.getSubtract());
	// if ("-1".equalsIgnoreCase(localData.getRealSubtract())) {
	// localData.setScored(false);
	// }
	// localData.setScoreTime(netData.getScoreTime());
	// return localData;
	// }

}
