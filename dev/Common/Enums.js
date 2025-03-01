/* eslint quote-props: 0 */

export const

/**
 * @enum {string}
 */
Scope = {
	MessageList: 'MessageList',
	FolderList: 'FolderList',
	MessageView: 'MessageView',
	Settings: 'Settings'
},

/**
 * @enum {number}
 */
UploadErrorCode = {
	Normal: 0,
	FileIsTooBig: 1,
	FilePartiallyUploaded: 3,
	NoFileUploaded: 4,
	MissingTempFolder: 6,
	OnSavingFile: 7,
	FileType: 98,
	Unknown: 99
},

/**
 * @enum {number}
 */
SaveSettingsStep = {
	Animate: -2,
	Idle: -1,
	TrueResult: 1,
	FalseResult: 0
},

/**
 * @enum {number}
 */
Notification = {
	RequestError: 1,
	RequestAborted: 2,
	RequestTimeout: 3,

	// Global
	InvalidToken: 101,
	AuthError: 102,

	// User
	ConnectionError: 104,
	DomainNotAllowed: 109,
	AccountNotAllowed: 110,

	ContactsSyncError: 140,

	CantGetMessageList: 201,
	CantGetMessage: 202,
	CantDeleteMessage: 203,
	CantMoveMessage: 204,
	CantCopyMessage: 205,

	CantSaveMessage: 301,
	CantSendMessage: 302,
	InvalidRecipients: 303,

	CantSaveFilters: 351,
	CantGetFilters: 352,
	CantActivateFiltersScript: 353,
	CantDeleteFiltersScript: 354,
//	FiltersAreNotCorrect: 355,

	CantCreateFolder: 400,
	CantRenameFolder: 401,
	CantDeleteFolder: 402,
	CantSubscribeFolder: 403,
	CantUnsubscribeFolder: 404,
	CantDeleteNonEmptyFolder: 405,

//	CantSaveSettings: 501,

	DomainAlreadyExists: 601,

	DemoSendMessageError: 750,
	DemoAccountError: 751,

	AccountAlreadyExists: 801,
	AccountDoesNotExist: 802,
	AccountSwitchFailed: 803,

	MailServerError: 901,
	ClientViewError: 902,
	InvalidInputArgument: 903,

	JsonFalse: 950,
	JsonParse: 952,
//	JsonTimeout: 953,

	UnknownNotification: 998,
	UnknownError: 999,

	// Admin
	CantInstallPackage: 701,
	CantDeletePackage: 702,
	InvalidPluginPackage: 703,
	UnsupportedPluginPackage: 704,
	CantSavePluginSettings: 705
};
