/** @file       FormatConversionDefine.h
 *  @note       HangZhou Hikvision Digital Technology Co., Ltd. All Right Reserved.
 *  @brief      Definitions of struct/enum/variables/constant/error-code used by APIs of Media Format Conversion dynamic library
 *
 *  @author     Media Play SDK Team of Hikvision
 *
 *  @version    V4.1.0
 *  @date       2014/03/03
 *
 *  @warning
 */
#ifndef _FC_DEFINE_H_
#define _FC_DEFINE_H_

 /**
  *  @brief  Definitions in Windows OS
  */
  //#ifdef _WINDOWS
#ifdef _WIN32
#ifdef FORMATCONVERSION_EXPORTS
#define FC_API __declspec(dllexport)
#else
#define FC_API __declspec(dllimport)
#endif
#endif/*_WINDOWS*/

  /**
   *  @brief  Definitions in linux OS
   *  @note   linux platform will use XWindow (x11)
   */
#ifdef __linux__
#define FC_API
#define __stdcall
#endif/*__linux__*/

   /**
	*  @brief  Definitions in MAX OS
	*  @note   apple we will use NSView , here type it as void *
	*/
#ifdef __APPLE__
#define FC_API
#define __stdcall
#endif/*__APPLE__*/

	/* 定义格式转换句柄类型 */
typedef void* FCHANDLE;

/* 状态码定义 */
#define FC_OK               0           ///< 成功，无错误
#define FC_E_HANDLE         0x80000000  ///< 错误或无效的句柄
#define FC_E_SUPPORT        0x80000001  ///< 不支持的功能
#define FC_E_BUFOVER        0x80000002  ///< 缓存已满
#define FC_E_CALLORDER      0x80000003  ///< 函数调用顺序错误
#define FC_E_PARAMETER      0x80000004  ///< 错误的参数
#define FC_E_NEEDMOREDATA   0x80000005  ///< 需要更多的数据
#define FC_E_RESOURCE       0x80000006  ///< 资源申请失败
#define FC_E_STREAM         0x80000007  ///< 码流出错
#define FC_E_DEMUXER        0x80000008  ///< demuxer内的异常错误
#define FC_E_MUXER          0x80000009  ///< muxer内的异常错误
#define FC_E_DECODER        0x8000000a  ///< decoder模块出错
#define FC_E_ENCODER        0x8000000b  ///< encoder模块出错
#define FC_E_POSTPROC       0x8000000c  ///< 后处理
#define FC_E_UNKNOW         0x800000ff  ///< 未知的错误

/* 码流封装格式 */
typedef enum FC_FormatType
{
	/* format types 与HKMI定义保持一致 */
	FC_FORMAT_NULL = 0x0,      ///< 无封装
	FC_FORMAT_HIK = 0x0001,   ///< 海康私有封装
	FC_FORMAT_MPEG2_PS = 0x0002,   ///< PS封装
	FC_FORMAT_MPEG2_TS = 0x0003,   ///< TS封装
	FC_FORMAT_RTP = 0x0004,   ///< RTP封装

	/* 以下封装格式海康基线暂不支持 */
	FC_FORMAT_MP4 = 0x0005,   ///< MP4封装
	FC_FORMAT_ASF = 0x0006,   ///< ASF封装
	FC_FORMAT_AVI = 0x0007,   ///< AVI封装

	/* 以下仅转码库定义 */
	FC_FORMAT_MOV = 0x0020,
	FC_FORMAT_3GP = 0x0021,
	FC_FORMAT_MKV = 0x0022,
	FC_FORMAT_WEBM = 0x0023,
	FC_FORMAT_FLV = 0x0024,
	FC_FORMAT_SWF,
	FC_FORMAT_RM,
};

/* 音视频编码格式 */
typedef enum FC_CodecType
{
	FC_CODEC_NONE = 0x0,      ///< 无编码

	/* video codecs 与HKMI定义保持一致。[0x0800以下为海康保留视频编码] */
	FC_CODEC_V_HIK264 = 0x0001,   ///< 海康编码
	FC_CODEC_V_MPEG2 = 0x0002,   ///< MPEG2
	FC_CODEC_V_MPEG4 = 0x0003,   ///< MPEG4
	FC_CODEC_V_MJPEG = 0x0004,   ///< MJPEG
	FC_CODEC_V_H265 = 0x0005,   ///< 标准H265
	FC_CODEC_V_H264 = 0x0100,   ///< 标准H264

	/* 以下视频编码，海康基线暂不支持 */
	FC_CODEC_V_YV12 = 0x0801,
	FC_CODEC_V_H263,
	FC_CODEC_V_FFH264,
	FC_CODEC_V_MSMPEG4V1 = 0x0811,
	FC_CODEC_V_MSMPEG4V2 = 0x0812,
	FC_CODEC_V_MSMPEG4V3 = 0x0813,
	FC_CODEC_V_WMV1 = 0x0821,
	FC_CODEC_V_WMV2 = 0x0822,
	FC_CODEC_V_FLV1,
	FC_CODEC_V_FLASHSV,
	FC_CODEC_V_VP8,
	FC_CODEC_V_MXPEG,

	/* audio codecs 与HKMI定义保持一致。 [0x8000以下为海康保留音频编码]*/
	FC_CODEC_A_MP2 = 0x2000,  ///< MP2
	FC_CODEC_A_AAC = 0x2001,  ///< AAC
	FC_CODEC_A_PCMU = 0x7110,  ///< G711U
	FC_CODEC_A_PCMA = 0x7111,  ///< G711A
	FC_CODEC_A_G722 = 0x7221,  ///< G722
	FC_CODEC_A_G723_1 = 0x7231,  ///< G7231
	FC_CODEC_A_G726 = 0x7262,  ///< G726
	FC_CODEC_A_G729 = 0x7290,  ///< G729

	/* 以下音频编码，海康基线暂不支持 */
	FC_CODEC_A_PCM = 0x8001,
	FC_CODEC_A_MP3,
	FC_CODEC_A_AMR_NB,
	FC_CODEC_A_AMR_WB,
	FC_CODEC_A_AC3,
	FC_CODEC_A_DTS,
	FC_CODEC_A_VORBIS,
	FC_CODEC_A_DVAUDIO,
	FC_CODEC_A_WMAV1 = 0x8010,
	FC_CODEC_A_WMAV2 = 0x8011,
	FC_CODEC_A_WMAVOICE,
	FC_CODEC_A_WMAPRO,
	FC_CODEC_A_WMALOSSLESS,
	FC_CODEC_A_FLAC,
};

//输入的数据类型
typedef enum FC_DataType
{
	FC_MULTI_DATA = 0x0,      ///< 混合流
	FC_VIDEO_DATA,                      ///< 视频流
	FC_AUDIO_DATA,                      ///< 音频流
	FC_PRIVATE_DATA,                    ///< 私有流
	FC_VIDEO_PARA,                      ///< 视频参数
	FC_AUDIO_PARA,                      ///< 音频参数
	FC_PRIVATE_PARA,                    ///< 私有参数
};

#define FC_MAX_TRACK_COUNT      8       ///< 最大轨道数

/* 视频信息结构体 */
typedef struct FC_VIDEO_INFO_STRU
{
	FC_CodecType        enCodec;        ///< 视频编码

	/*
	 * 如果enCodec = FC_CODEC_NONE，即视频编码自适应，则以下参数设置无效
	 */
	unsigned int        nTrackId;       ///< 轨道号 [暂未使用]
	unsigned int        nBitRate;       ///< 重编码后的码率
	float               fFrameRate;     ///< 重编码后的视频帧率
	unsigned short      nWidth;         ///< 经重采样及编码后的图像宽度[16倍数]
	unsigned short      nHeight;        ///< 经重采样及编码后的图像高度[16倍数]

}FC_VIDEO_INFO;

/* 音频信息结构体 */
typedef struct FC_AUDIO_INFO_STRU
{
	FC_CodecType        enCodec;        ///< 音频编码

	/*
	 * 如果enCodec = FC_CODEC_NONE，即音频编码自适应，则以下参数设置无效
	 */
	unsigned int        nTrackId;       ///< 轨道号 [暂未使用]
	unsigned short      nChannels;      ///< 声道数
	unsigned short      nBitsPerSample; ///< 样位率
	unsigned int        nSamplesRate;   ///< 采样率
	unsigned int        nBitRate;       ///< 比特率

}FC_AUDIO_INFO;

/* 私有信息结构体 */
typedef struct FC_PRIVT_INFO_STRU
{
	unsigned int        nType;          ///< 私有数据类型
	unsigned int        nTrackId;       ///< 轨道号 [暂未使用]
}FC_PRIVT_INFO;

/** @struct MEDIA_INFO
 *  @brief  媒体信息
 */
typedef struct FC_MEDIA_INFO_STRU
{
	FC_FormatType       enSystemFormat;     ///< 封装格式

	/* 音视频流都要求输出时，填1；如果不输出音频，则音频流数量填0 */
	unsigned int        nVideoStreamCount;  ///< 视频流数量，最大值：FC_MAX_TRACK_COUNT
	unsigned int        nAudioStreamCount;  ///< 音频流数量，最大值：FC_MAX_TRACK_COUNT
	unsigned int        nPrivtStreamCount;  ///< 私有流数量，最大值：FC_MAX_TRACK_COUNT

	/* 以下参数选填；如果需要自适应，可以都填0；优先转封装*/

	/* 视频信息 */
	FC_VIDEO_INFO       stVideoInfo[FC_MAX_TRACK_COUNT];

	/* 音频信息 */
	FC_AUDIO_INFO       stAudioInfo[FC_MAX_TRACK_COUNT];

	/* 私有信息 */
	FC_PRIVT_INFO       stPrivtInfo[FC_MAX_TRACK_COUNT];

	unsigned int        nReserved[4];       ///< 保留字段

} FC_MEDIA_INFO;


/* 网络协议类型 */
#define FC_PROTOCOL_NULL        0       ///< 无网络协议
#define FC_PROTOCOL_HIK         1       ///< 海康私有协议
#define FC_PROTOCOL_RTSP        2       ///< RTSP协议 [暂未实现]

/* 交互信息类型 */
#define FC_SESSION_MEDIADATA    0       ///< 未知网络协议或未知交互信息类型，可以直接使用媒体数据进行交互，建议输入100k以上数据量
#define FC_SESSION_HIK          1       ///< 40字节的海康私有交互信息/海康媒体信息头/海康数据头
#define FC_SESSION_SDP          2       ///< SDP,rfc4566定义 [暂未实现]

/* 输出包类型 */
#define FC_UNKNOW_PACKET        0       ///< 未知类型
#define FC_VIDEO_PACKET         1       ///< 视频包
#define FC_AUDIO_PACKET         2       ///< 音频包
#define FC_PRIVT_PACKET         3       ///< 私有包
#define FC_HIK_FILE_HEADER      4       ///< 海康文件头

/** @struct FC_SESSION_INFO
 *  @brief  交互信息结构体
 */
typedef struct FC_SESSION_INFO_STRU
{
	unsigned int        nSessionInfoType;   ///< 交互信息类型，FC_SESSION_MEDIADATA / FC_SESSION_HIK / FC_SESSION_SDP
	unsigned int        nSessionInfoLen;    ///< 交互信息长度
	unsigned char       *pSessionInfoData;  ///< 交互信息数据
	unsigned int        nReserved[4];       ///< 保留字段
} FC_SESSION_INFO;

#endif //_FC_DEFINE_H_
