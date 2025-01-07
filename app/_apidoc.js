/**
     * @api {patch} /post/:id Post update
     * @apiDescription Updating posts, normal users can only update their own posts, while admins can update everyone's
     * @apiParam {Number} id Id of the post to be updated
     * @apiGroup Post
     * @apiUse HeadersWithToken
     * @apiBody {String{min:10 - max:65534}} [post] Text of the new post
     * @apiBody {String{max: 500KB}} [image] Base64 encoded image for the new post
     * @apiBody {Number} [likes] The new number of likes on the post
     * @apiError Unauthenticated User making the request is not logged in or has outdated access token.
     * @apiError ThePostFieldMustBeAtLeast10Characters <code>post</code> must be at least 10 characters.
     * @apiError ThePostFieldMustNotBeGreaterThan65534Characters. <code>post</code> must be below 65534 characters.
     * @apiError TheImageMustBeOfTypeJpeg,jpg,png <code>image</code> must be of type jpeg, jpg, png
     * @apiError NoQueryResultsForModel:id Post with <code>id</code> could not be found
     * @apiError TheLikesFieldMustBeANumber <code>likes</code> field must be a number
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 422 Unprocessable Content
     *       {
     *           "message": "The post field must be at least 10 characters.",
     *           "errors": {
     *               "post": [
     *                   "The post field must be at least 10 characters."
     *               ]
     *           }
     *       }
     * @apiPermission normal user
     * @apiSuccess {String} message Information about the post update.
     * @apiSuccess {Object} post Data of the updated post.
     * @apiSuccess {Number} post.id <code>id</code> of the updated post.
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *           "message": "Post updated successfully",
     *           "post": {
     *               "id": 3
     *           }
     *       }
     *    @apiVersion 0.1.0
     */

/**
     * @api {get} /post/:sort_by/:sort_dir Get all posts
     * @apiDescription Getting all posts, admin users get additional fields returned in the response, compared to normal users
     * @apiParam {String} sort_by Field the result is sorted by
     * @apiParam {String="asc","desc"} sort_dir Sort direction
     * @apiGroup Post
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated. User making the request is not logged in or has outdated access token.
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *       {
     *           "message": "Unauthenticated,"
     *       }
     * @apiPermission normal user
     * @apiSuccess (Success-Normal user) {Object} posts Data of the posts.
     * @apiSuccess (Success-Normal user) {Number} post.current_page Current page of the pagination.
     * @apiSuccess (Success-Normal user) {Object} post.data Array of all the post data.
     * @apiSuccess (Success-Normal user) {id} post.data.id Post's <code>id</code>.
     * @apiSuccess (Success-Normal user) {String} post.data.post Post's text.
     * @apiSuccess (Success-Normal user) {Date} post.data.created_at When the <code>post</code> was created.
     * @apiSuccess (Success-Normal user) {Date} post.data.modified_at When the <code>post</code> was last modified.
     * 
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Object} post Data of the requested post
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} post.deleted_at When the post was deleted
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *           "posts": {
     *               "current_page": 1,
     *               "data": [
     *                   {
     *                       "id": 1,
     *                       "post": "Alice, who was beginning to feel very uneasy: to.",
     *                       "created_at": "2024-11-23T13:19:26.000000Z",
     *                       "updated_at": "2024-11-23T13:19:26.000000Z"
     *                   },
     *                   {
     *                       "id": 2,
     *                       "post": "Dinah, tell me who YOU are, first.' 'Why?' said.",
     *                       "created_at": "2024-11-23T13:19:26.000000Z",
     *                       "updated_at": "2024-11-23T13:19:26.000000Z"
     *                   },
     *                   {
     *                       "id": 5,
     *                       "post": "I can guess that,' she added in an undertone to.",
     *                       "created_at": "2024-11-23T13:19:26.000000Z",
     *                       "updated_at": "2024-11-23T13:19:26.000000Z"
     *                   },
     *                   {
     *                       "id": 6,
     *                       "post": "Gryphon. '--you advance twice--' 'Each with a.",
     *                       "created_at": "2024-11-23T13:19:26.000000Z",
     *                       "updated_at": "2024-11-23T13:19:26.000000Z"
     *                   },
     *                   {
     *                       "id": 7,
     *                       "post": "I should be like then?' And she went round the.",
     *                       "created_at": "2024-11-23T13:19:26.000000Z",
     *                       "updated_at": "2024-11-23T13:19:26.000000Z"
     *                   },
     *                   {
     *                       "id": 8,
     *                       "post": "At this moment the door with his tea spoon at.",
     *                       "created_at": "2024-11-23T13:19:26.000000Z",
     *                       "updated_at": "2024-11-23T13:19:26.000000Z"
     *                   },
     *                   {
     *                       "id": 9,
     *                       "post": "And in she went. Once more she found she had.",
     *                       "created_at": "2024-11-23T13:19:26.000000Z",
     *                       "updated_at": "2024-11-23T13:19:26.000000Z"
     *                   },
     *                   {
     *                       "id": 10,
     *                       "post": "Queen added to one of them didn't know it to the.",
     *                       "created_at": "2024-11-23T13:19:26.000000Z",
     *                       "updated_at": "2024-11-23T13:19:26.000000Z"
     *                   },
     *                   {
     *                       "id": 11,
     *                       "post": "Yeah body, light weight",
     *                       "created_at": "2024-11-26T17:12:34.000000Z",
     *                       "updated_at": "2024-11-26T17:12:34.000000Z"
     *                   }
     *               ],
     *               "first_page_url": "http://localhost:8000/api/post/id/asc?page=1",
     *               "from": 1,
     *               "last_page": 1,
     *               "last_page_url": "http://localhost:8000/api/post/id/asc?page=1",
     *               "links": [
     *                   {
     *                       "url": null,
     *                       "label": "&laquo; Previous",
     *                       "active": false
     *                   },
     *                   {
     *                       "url": "http://localhost:8000/api/post/id/asc?page=1",
     *                       "label": "1",
     *                       "active": true
     *                   },
     *                   {
     *                       "url": null,
     *                       "label": "Next &raquo;",
     *                       "active": false
     *                   }
     *               ],
     *               "next_page_url": null,
     *               "path": "http://localhost:8000/api/post/id/asc",
     *               "per_page": 30,
     *               "prev_page_url": null,
     *               "to": 9,
     *               "total": 9
     *           }
     *       }
     *    @apiVersion 0.1.0
     */

/**
     * @api {get} /user/all/:sort_by/:sort_dir Get all users
     * @apiDescription Getting user data, admin users get additional fields returned in the response, compared to normal users
     * @apiParam {String} sort_by Field to be used to order the posts by
     * @apiParam {String="asc","desc"} sort_dir Order direction for the posts
     * @apiGroup User
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated. User making the request is not logged in or has outdated access token.
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *       {
     *           "message": "Unauthenticated,"
     *       }
     * @apiPermission normal user
     * @apiSuccess (Success-Normal user) {Object} users Data of all the users.
     * @apiSuccess (Success-Normal user) {Number} users.current_page Current page of the pagination.
     * @apiSuccess (Success-Normal user) {Object} users.data Array of the returnded users.
     * @apiSuccess (Success-Normal user) {Number} users.data.id User <code>id</code>.
     * @apiSuccess (Success-Normal user) {String} users.data.name User <code>name</code>.
     * 
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Object} user.data Data of the requested user
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} user.data.deleted_at When the user was deleted
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} user.data.modified_at When the user was last modified
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} user.data.created_at When the user was created
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *          "users": {
     *              "current_page": 1,
     *              "data": [
     *                  {
     *                      "id": 11,
     *                      "name": "lakatos"
     *                  },
     *                  {
     *                      "id": 1,
     *                      "name": "Test User0jkkjkj"
     *                  },
     *                  ...
     *                  {
     *                      "id": 10,
     *                      "name": "Test User9"
     *                  }
     *              ],
     *              "first_page_url": "http://localhost:8000/api/user/all/name/asc?page=1",
     *              "from": 1,
     *              "last_page": 1,
     *              "last_page_url": "http://localhost:8000/api/user/all/name/asc?page=1",
     *              "links": [
     *                  {
     *                      "url": null,
     *                      "label": "&laquo; Previous",
     *                      "active": false
     *                  },
     *                  {
     *                      "url": "http://localhost:8000/api/user/all/name/asc?page=1",
     *                      "label": "1",
     *                      "active": true
     *                  },
     *                  {
     *                      "url": null,
     *                      "label": "Next &raquo;",
     *                      "active": false
     *                  }
     *              ],
     *              "next_page_url": null,
     *              "path": "http://localhost:8000/api/user/all/name/asc",
     *              "per_page": 30,
     *              "prev_page_url": null,
     *              "to": 11,
     *              "total": 11
     *          }
     *      }
     *    @apiVersion 0.1.0
     */

/**
     * @api {get} /user/:id Get user data
     * @apiDescription Getting user data, admin users get additional fields returned in the response, compared to normal users
     * @apiParam {Number} id Id of user to be queried
     * @apiGroup User
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated. User making the request is not logged in or has outdated access token.
     * @apiError NoQueryResultsForModel:id User with <code>id</code> could not be found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *       {
     *           "message": "Unauthenticated,"
     *       }
     * @apiPermission normal user
     * @apiSuccess (Success-Normal user) {Object} user Data of the requested user.
     * @apiSuccess (Success-Normal user) {Number} user.id   User's <code>id</code>.
     * @apiSuccess (Success-Normal user) {Number} user.name User's <code>name</code>.
     * @apiSuccess (Success-Normal user) {Number} user.email User's <code>email</code>.
     * @apiSuccess (Success-Normal user) {Number} user.deaths User's <code>deaths</code>.
     * @apiSuccess (Success-Normal user) {Number} user.kills User's <code>kills</code>.
     * @apiSuccess (Success-Normal user) {Number} user.waves User's <code>waves</code>.
     * @apiSuccess (Success-Normal user) {Number} user.boss1lvl User's <code>boss1lvl</code>.
     * @apiSuccess (Success-Normal user) {Number} user.boss2lvl User's <code>boss2lvl</code>.
     * @apiSuccess (Success-Normal user) {Number} user.boss3lvl User's <code>boss3lvl</code>.
     * @apiSuccess (Success-Normal user) {Date} user.created_at When the user was created.
     * @apiSuccess (Success-Normal user) {Object} achievements Achievements of the selected user.
     * @apiSuccess (Success-Normal user) {Number} achievements.id Achievement <code>id</code>.
     * @apiSuccess (Success-Normal user) {String} achievements.name Achievement <code>name</code>.
     * @apiSuccess (Success-Normal user) {String} achievements.description Achievement <code>description</code>.
     * @apiSuccess (Success-Normal user) {String} achievements.field Column name in the user table.
     * @apiSuccess (Success-Normal user) {Number} achievements.threshold Threshold of the achievement.
     * @apiSuccess (Success-Normal user) {Object} achievements.pivot Data from the pivot table.
     * @apiSuccess (Success-Normal user) {Number} achievements.pivot.user_id User id from the pivot table.
     * @apiSuccess (Success-Normal user) {Number} achievements.pivot.achievement_id Achievement id from the pivot table.
     * 
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Object} user Data of the requested user
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} user.deleted_at When the user was deleted
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} user.modified_at When the user was last modified
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *           "user": {
     *               "id": 5,
     *               "name": "Test User4",
     *               "email": "test7@example.com",
     *               "deaths": 5,
     *               "kills": 6,
     *               "waves": 5,
     *               "boss1lvl": 9,
     *               "boss2lvl": 7,
     *               "boss3lvl": 10,
     *               "created_at": "2024-11-05T11:49:11.000000Z"
     *           },
     *           "achievements": [
     *               {
     *                   "id": 3,
     *                   "name": "But, now.",
     *                   "description": "And will.",
     *                   "field": "boss3lvl",
     *                   "threshold": 5,
     *                   "pivot": {
     *                       "user_id": 5,
     *                       "achievement_id": 3
     *                   }
     *               }
     *           ]
     *       }
     *    @apiVersion 0.1.0
     */

/**
     * @api {get} /user/search/:sort_by/:sort_dir/:search_for Search for users
     * @apiDescription Getting user data, admin users get additional fields returned in the response, compared to normal users
     * @apiParam {String} sort_by Field to be used to order the posts by
     * @apiParam {String="asc","desc"} sort_dir Order direction for the posts
     * @apiParam {String} search_for Keyword to search for in user names
     * @apiGroup User
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated. User making the request is not logged in or has outdated access token.
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *       {
     *           "message": "Unauthenticated,"
     *       }
     * @apiPermission normal user
     * @apiSuccess (Success-Normal user) {Object} users Data of all the users.
     * @apiSuccess (Success-Normal user) {Number} users.current_page Current page of the pagination.
     * @apiSuccess (Success-Normal user) {Object} users.data Data of the returnded users.
     * @apiSuccess (Success-Normal user) {Number} users.data.id User <code>id</code>.
     * @apiSuccess (Success-Normal user) {String} users.data.name User <code>name</code>.
     * 
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Object} user.data Data of the requested user
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} user.data.deleted_at When the user was deleted
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} user.data.modified_at When the user was last modified
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} user.data.created_at When the user was created
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *           "users": {
     *               "current_page": 1,
     *               "data": [
     *                   {
     *                       "id": 9,
     *                       "name": "Test User8"
     *                   },
     *                   {
     *                       "id": 10,
     *                       "name": "Test User9"
     *                   },
     *                   {
     *                       "id": 8,
     *                       "name": "Test User7"
     *                   },
     *                   {
     *                       "id": 7,
     *                       "name": "Test User6"
     *                   },
     *                   {
     *                       "id": 5,
     *                       "name": "Test User4"
     *                   },
     *                   {
     *                       "id": 6,
     *                       "name": "Test User5"
     *                   },
     *                   {
     *                       "id": 4,
     *                       "name": "Test User3"
     *                   },
     *                   {
     *                       "id": 3,
     *                       "name": "Test User2"
     *                   },
     *                   {
     *                       "id": 2,
     *                       "name": "Test User1"
     *                   },
     *                   {
     *                       "id": 1,
     *                       "name": "Test User0"
     *                   }
     *               ],
     *               "first_page_url": "http://localhost:8000/api/user/search/created_at/desc/test?page=1",
     *               "from": 1,
     *               "last_page": 1,
     *               "last_page_url": "http://localhost:8000/api/user/search/created_at/desc/test?page=1",
     *               "links": [
     *                   {
     *                       "url": null,
     *                       "label": "&laquo; Previous",
     *                       "active": false
     *                   },
     *                   {
     *                       "url": "http://localhost:8000/api/user/search/created_at/desc/test?page=1",
     *                       "label": "1",
     *                       "active": true
     *                   },
     *                   {
     *                       "url": null,
     *                       "label": "Next &raquo;",
     *                       "active": false
     *                   }
     *               ],
     *               "next_page_url": null,
     *               "path": "http://localhost:8000/api/user/search/created_at/desc/test",
     *               "per_page": 30,
     *               "prev_page_url": null,
     *               "to": 10,
     *               "total": 10
     *           }
     *       }
     *    @apiVersion 0.1.0
     */

    /**
     * @api {get} /post/search/:sort_by/:sort_dir/:search_for Search for posts
     * @apiDescription Search for posts, admin users get additional fields returned in the response, compared to normal users
     * @apiParam {String} sort_by Field the result is sorted by
     * @apiParam {String="asc","desc"} sort_dir Sort direction
     * @apiParam {String} search_for Keyword to search for
     * @apiGroup Post
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated. User making the request is not logged in or has outdated access token.
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *       {
     *           "message": "Unauthenticated,"
     *       }
     * @apiPermission normal user
     * @apiSuccess (Success-Normal user) {Object} posts Data of the posts.
     * @apiSuccess (Success-Normal user) {Number} post.current_page Current page of the pagination.
     * @apiSuccess (Success-Normal user) {Object} post.data Array of all the post data.
     * @apiSuccess (Success-Normal user) {id} post.data.id Post's <code>id</code>.
     * @apiSuccess (Success-Normal user) {String} post.data.post Post's text.
     * @apiSuccess (Success-Normal user) {Date} post.data.created_at When the <code>post</code> was created.
     * @apiSuccess (Success-Normal user) {Date} post.data.modified_at When the <code>post</code> was last modified.
     * 
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Object} post Data of the requested post
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} post.deleted_at When the post was deleted
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *           "posts": {
     *               "current_page": 1,
     *               "data": [
     *                   {
     *                       "id": 1,
     *                       "post": "Alice, who was beginning to feel very uneasy: to.",
     *                       "created_at": "2024-11-23T13:19:26.000000Z",
     *                       "updated_at": "2024-11-23T13:19:26.000000Z"
     *                   },
     *                   {
     *                       "id": 5,
     *                       "post": "I can guess that,' she added in an undertone to.",
     *                       "created_at": "2024-11-23T13:19:26.000000Z",
     *                       "updated_at": "2024-11-23T13:19:26.000000Z"
     *                   },
     *                   {
     *                       "id": 10,
     *                       "post": "Queen added to one of them didn't know it to the.",
     *                       "created_at": "2024-11-23T13:19:26.000000Z",
     *                       "updated_at": "2024-11-23T13:19:26.000000Z"
     *                   }
     *               ],
     *               "first_page_url": "http://localhost:8000/api/post/search/created_at/desc/to?page=1",
     *               "from": 1,
     *               "last_page": 1,
     *               "last_page_url": "http://localhost:8000/api/post/search/created_at/desc/to?page=1",
     *               "links": [
     *                   {
     *                       "url": null,
     *                       "label": "&laquo; Previous",
     *                       "active": false
     *                   },
     *                   {
     *                       "url": "http://localhost:8000/api/post/search/created_at/desc/to?page=1",
     *                       "label": "1",
     *                       "active": true
     *                   },
     *                   {
     *                       "url": null,
     *                       "label": "Next &raquo;",
     *                       "active": false
     *                   }
     *               ],
     *               "next_page_url": null,
     *               "path": "http://localhost:8000/api/post/search/created_at/desc/to",
     *               "per_page": 30,
     *               "prev_page_url": null,
     *               "to": 3,
     *               "total": 3
     *           }
     *       }
     *    @apiVersion 0.1.0
     */

     /**
     * @api {patch} /post/:id Post update
     * @apiDescription Updating posts, normal users can only update their own posts, while admins can update everyone's
     * @apiParam {Number} id Id of the post to be updated
     * @apiGroup Post
     * @apiUse HeadersWithToken
     * @apiBody {String{min:10 - max:65534}} [post] Text of the new post
     * @apiBody {String{max: 500KB}} [image] Base64 encoded image for the new post
     * @apiBody {Boolean} [likes] Wether we would like to like the post or not
     * @apiError Unauthenticated User making the request is not logged in or has outdated access token.
     * @apiError ThePostFieldMustBeAtLeast10Characters <code>post</code> must be at least 10 characters.
     * @apiError ThePostFieldMustNotBeGreaterThan65534Characters. <code>post</code> must be below 65534 characters.
     * @apiError TheImageMustBeOfTypeJpeg,jpg,png <code>image</code> must be of type jpeg, jpg, png
     * @apiError NoQueryResultsForModel:id Post with <code>id</code> could not be found
     * @apiError TheLikesFieldMustBeTrueOrFalse <code>likes</code> field must be a boolean value
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 422 Unprocessable Content
     *       {
     *           "message": "The post field must be at least 10 characters.",
     *           "errors": {
     *               "post": [
     *                   "The post field must be at least 10 characters."
     *               ]
     *           }
     *       }
     * @apiPermission normal user
     * @apiSuccess {String} message Information about the post update.
     * @apiSuccess {Object} post Data of the updated post.
     * @apiSuccess {Number} post.id <code>id</code> of the updated post.
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *           "message": "Post updated successfully",
     *           "post": {
     *               "id": 3
     *           }
     *       }
     *    @apiVersion 0.2.0
     */

     /**
     * @api {get} /post/:sort_by/:sort_dir Get all posts
     * @apiDescription Getting all posts, admin users get additional fields returned in the response, compared to normal users
     * @apiParam {String} sort_by Field the result is sorted by
     * @apiParam {String="asc","desc"} sort_dir Sort direction
     * @apiGroup Post
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated. User making the request is not logged in or has outdated access token.
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *       {
     *           "message": "Unauthenticated,"
     *       }
     * @apiPermission normal user
     * @apiSuccess (Success-Normal user) {Object} posts Data of the posts.
     * @apiSuccess (Success-Normal user) {Number} post.current_page Current page of the pagination.
     * @apiSuccess (Success-Normal user) {Object} post.data Array of all the post data.
     * @apiSuccess (Success-Normal user) {id} post.data.id Post's <code>id</code>.
     * @apiSuccess (Success-Normal user) {String} post.data.post Post's text.
     * @apiSuccess (Success-Normal user) {Number} post.data.likes Post's number of <code>likes</code>.
     * @apiSuccess (Success-Normal user) {Date} post.data.created_at When the <code>post</code> was created.
     * @apiSuccess (Success-Normal user) {Date} post.data.modified_at When the <code>post</code> was last modified.
     * 
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Object} post Data of the requested post
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} post.deleted_at When the post was deleted
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *          "posts": {
     *              "current_page": 1,
     *              "data": [
     *                  {
     *                      "id": 1,
     *                      "post": "Alice desperately: 'he's perfectly idiotic!' And.",
     *                      "likes": 1774,
     *                      "created_at": "2024-11-05T11:49:15.000000Z",
     *                      "updated_at": "2024-11-05T11:49:15.000000Z"
     *                  },
     *                  {
     *                      "id": 2,
     *                      "post": "These were the verses the White Rabbit, who said.",
     *                      "likes": 2597,
     *                      "created_at": "2024-11-05T11:49:15.000000Z",
     *                      "updated_at": "2024-11-05T11:49:15.000000Z"
     *                  },
     *                  ...
     *                  {
     *                      "id": 12,
     *                      "post": "What a nice gentleman :)",
     *                      "likes": 0,
     *                      "created_at": "2024-12-03T09:07:46.000000Z",
     *                      "updated_at": "2024-12-03T09:58:12.000000Z"
     *                  }
     *              ],
     *              "first_page_url": "http://localhost:8000/api/post/id/asc?page=1",
     *              "from": 1,
     *              "last_page": 1,
     *              "last_page_url": "http://localhost:8000/api/post/id/asc?page=1",
     *              "links": [
     *                  {
     *                      "url": null,
     *                      "label": "&laquo; Previous",
     *                      "active": false
     *                  },
     *                  {
     *                      "url": "http://localhost:8000/api/post/id/asc?page=1",
     *                      "label": "1",
     *                      "active": true
     *                  },
     *                  {
     *                      "url": null,
     *                      "label": "Next &raquo;",
     *                      "active": false
     *                  }
     *              ],
     *              "next_page_url": null,
     *              "path": "http://localhost:8000/api/post/id/asc",
     *              "per_page": 30,
     *              "prev_page_url": null,
     *              "to": 12,
     *              "total": 12
     *          }
     *      }
     *    @apiVersion 0.2.0
     */

     /**
     * @api {get} /post/search/:sort_by/:sort_dir/:search_for Search for posts
     * @apiDescription Search for posts, admin users get additional fields returned in the response, compared to normal users
     * @apiParam {String} sort_by Field the result is sorted by
     * @apiParam {String="asc","desc"} sort_dir Sort direction
     * @apiParam {String} search_for Keyword to search for
     * @apiGroup Post
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated. User making the request is not logged in or has outdated access token.
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *       {
     *           "message": "Unauthenticated,"
     *       }
     * @apiPermission normal user
     * @apiSuccess (Success-Normal user) {Object} posts Data of the posts.
     * @apiSuccess (Success-Normal user) {Number} post.current_page Current page of the pagination.
     * @apiSuccess (Success-Normal user) {Object} post.data Array of all the post data.
     * @apiSuccess (Success-Normal user) {id} post.data.id Post's <code>id</code>.
     * @apiSuccess (Success-Normal user) {String} post.data.post Post's text.
     * @apiSuccess (Success-Normal user) {Number} post.data.likes Number of <code>likes</code> on the post.
     * @apiSuccess (Success-Normal user) {Date} post.data.created_at When the <code>post</code> was created.
     * @apiSuccess (Success-Normal user) {Date} post.data.modified_at When the <code>post</code> was last modified.
     * 
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Object} post Data of the requested post
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} post.deleted_at When the post was deleted
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *      {
     *          "posts": {
     *              "current_page": 1,
     *              "data": [
     *                  {
     *                      "id": 5,
     *                      "post": "Alice: 'allow me to him: She gave me a good.",
     *                      "likes": 295,
     *                      "created_at": "2024-11-05T11:49:15.000000Z",
     *                      "updated_at": "2024-11-05T11:49:15.000000Z"
     *                  },
     *                  {
     *                      "id": 8,
     *                      "post": "I didn't know that you're mad?' 'To begin with,'.",
     *                      "likes": 1882,
     *                      "created_at": "2024-11-05T11:49:15.000000Z",
     *                      "updated_at": "2024-11-05T11:49:15.000000Z"
     *                  }
     *              ],
     *              "first_page_url": "http://localhost:8000/api/post/search/created_at/desc/to?page=1",
     *              "from": 1,
     *              "last_page": 1,
     *              "last_page_url": "http://localhost:8000/api/post/search/created_at/desc/to?page=1",
     *              "links": [
     *                  {
     *                      "url": null,
     *                      "label": "&laquo; Previous",
     *                      "active": false
     *                  },
     *                  {
     *                      "url": "http://localhost:8000/api/post/search/created_at/desc/to?page=1",
     *                      "label": "1",
     *                      "active": true
     *                  },
     *                  {
     *                      "url": null,
     *                      "label": "Next &raquo;",
     *                      "active": false
     *                  }
     *              ],
     *              "next_page_url": null,
     *              "path": "http://localhost:8000/api/post/search/created_at/desc/to",
     *              "per_page": 30,
     *              "prev_page_url": null,
     *              "to": 2,
     *              "total": 2
     *          }
     *      }
     *    @apiVersion 0.2.0
     */

        /**
     * @api {get} /user/:sort_by/:sort_dir Get user's own posts
     * @apiDescription Getting the user's own posts, admin users can view their deleted posts as well
     * @apiParam {String} sort_by Field to be used to order the posts by
     * @apiParam {String="asc","desc"} sort_dir Order direction for the posts
     * @apiGroup User
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated. User making the request is not logged in or has outdated access token.
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *       {
     *           "message": "Unauthenticated,"
     *       }
     * @apiPermission normal user
     * @apiSuccess (Success-Normal user) {Object} posts Data of all the posts of the logged in user.
     * @apiSuccess (Success-Normal user) {Number} posts.current_page Current page of the pagination.
     * @apiSuccess (Success-Normal user) {Object} posts.data Data of the returnded posts.
     * @apiSuccess (Success-Normal user) {Number} posts.data.id Post <code>id</code>.
     * @apiSuccess (Success-Normal user) {String} posts.data.post Text of the post.
     * @apiSuccess (Success-Normal user) {Date} posts.data.created_at When the post was created.
     * @apiSuccess (Success-Normal user) {Date} posts.data.updated_at When the post was last updated.
     * 
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Object} data Data of all the posts of the logged in user user.
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} posts.data.deleted_at When the post was deleted
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *           "posts": {
     *               "current_page": 1,
     *               "data": [
     *                   {
     *                       "id": 1,
     *                       "post": "Alice desperately: 'he's perfectly idiotic!' And.",
     *                       "created_at": "2024-11-05T11:49:15.000000Z",
     *                       "updated_at": "2024-11-05T11:49:15.000000Z",
     *                   },
     *                   {
     *                       "id": 2,
     *                       "post": "These were the verses the White Rabbit, who said.",
     *                       "created_at": "2024-11-05T11:49:15.000000Z",
     *                       "updated_at": "2024-11-05T11:49:15.000000Z",
     *                   },
     *                   {
     *                       "id": 3,
     *                       "post": "Alice's side as she fell past it. 'Well!'.",
     *                       "created_at": "2024-11-05T11:49:15.000000Z",
     *                       "updated_at": "2024-11-05T11:49:15.000000Z",
     *                   },
     *                   {
     *                       "id": 10,
     *                       "post": "Alice had no pictures or conversations in it.",
     *                       "created_at": "2024-11-05T11:49:15.000000Z",
     *                       "updated_at": "2024-11-05T11:49:15.000000Z",
     *                   }
     *               ],
     *               "first_page_url": "http://localhost:8000/api/user/id/asc?page=1",
     *               "from": 1,
     *               "last_page": 1,
     *               "last_page_url": "http://localhost:8000/api/user/id/asc?page=1",
     *               "links": [
     *                   {
     *                       "url": null,
     *                       "label": "&laquo; Previous",
     *                       "active": false
     *                   },
     *                   {
     *                       "url": "http://localhost:8000/api/user/id/asc?page=1",
     *                       "label": "1",
     *                       "active": true
     *                   },
     *                   {
     *                       "url": null,
     *                       "label": "Next &raquo;",
     *                       "active": false
     *                   }
     *               ],
     *               "next_page_url": null,
     *               "path": "http://localhost:8000/api/user/id/asc",
     *               "per_page": 30,
     *               "prev_page_url": null,
     *               "to": 4,
     *               "total": 4
     *           }
     *       }
     *    @apiVersion 0.1.0
     */

         /**
     * @api {post} /achievement Achievement creation
     * @apiGroup Achievement
     * @apiUse HeadersWithToken
     * @apiBody {String} name Name of the achievement
     * @apiBody {String} field The column name from the user table when handing out the achievement
     * @apiBody {Number} threshold The amount that has to reached to be awarded the achievement
     * @apiBody {String} description The description of the achievement
     * @apiError Unauthenticated User making the request is not logged in or has outdated access token.
     * @apiError TheNameFieldIsRequired The <code>name</code> field is required
     * @apiError TheFieldFieldIsRequired The <code>field</code> field is required
     * @apiError TheThresholdFieldIsRequired The <code>threshold</code> field is required
     * @apiError TheThresholdFieldIsMustBeANumber The <code>threshold</code> field must be a number
     * @apiError TheDescriptionFieldIsRequired The <code>description</code> field is required
     * @apiError InvalidAbilityProvided The user is not authorized to create achievements
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 422 Unprocessable Content
     *       {
     *           "message": "The field field is required. (and 2 more errors)",
     *           "errors": {
     *               "field": [
     *                   "The field field is required."
     *               ],
     *               "threshold": [
     *                   "The threshold field is required."
     *               ],
     *               "description": [
     *                   "The description field is required."
     *               ]
     *           }
     *       }
     * @apiPermission admin
     * @apiSuccess {String} message Information about the achievement creation.
     * @apiSuccess {Object} achievement Data of the newly created achievement.
     * @apiSuccess {Number} achievement.id <code>id</code> of the new achievement.
     * @apiSuccess {String} achievement.name <code>name</code> of the new achievement.
     *    @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *           "message": "Achievement created successfully",
     *           "post": {
     *               "id": 11
     *           }
     *       }
     *    @apiVersion 0.1.0
     */

         /**
     * @api {patch} /achievement/:id Achievement update
     * @apiParam {Number} id Id of achievement to be updated
     * @apiGroup Achievement
     * @apiUse HeadersWithToken
     * @apiBody {String} [name] Name of the achievement
     * @apiBody {String} [field] The column name from the user table when handing out the achievement
     * @apiBody {Number} [threshold] The amount that has to reached to be awarded the achievement
     * @apiBody {String} [description] The description of the achievement
     * @apiError Unauthenticated User making the request is not logged in or has outdated access token.
     * @apiError TheThresholdFieldIsMustBeANumber The <code>threshold</code> field must be a number
     * @apiError InvalidAbilityProvided The user is not authorized to update achievements
     * @apiError NoQueryResultsForModel:id Achievement with <code>id</code> could not be found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 422 Unprocessable Content
     *       {
     *           "message": "The threshold field must be a number.",
     *           "errors": {
     *               "threshold": [
     *                   "The threshold field must be a number."
     *               ]
     *           }
     *       }
     * @apiPermission admin
     * @apiSuccess {String} message Information about the achievement update.
     * @apiSuccess {Object} achievement Data of the updated achievement.
     * @apiSuccess {Number} achievement.id <code>id</code> of the updated achievement.
     *    @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *           "message": "Achievement updated successfully",
     *           "achievement": {
     *               "id": 10
     *           }
     *       }
     *    @apiVersion 0.1.0
     */

         /**
     * @api {get} /achievement Get all achievements
     * @apiGroup Achievement
     * @apiUse HeadersWithToken
     * @apiPermission none
     * @apiSuccess {Object} achievements Array of all the achievements.
     * @apiSuccess {Number} achievements.id <code>id</code> of achievement.
     * @apiSuccess {String} achievements.name <code>name</code> of achievement.
     * @apiSuccess {String} achievements.field <code>field</code> of achievement.
     * @apiSuccess {String} achievements.description <code>description</code> of achievement.
     * @apiSuccess {Number} achievements.threshold <code>threshold</code> of achievement.
     *    @apiSuccessExample {json} Success-Response:
     *       {
     *           "achievements": [
     *               {
     *                   "id": 2,
     *                   "name": "On which.",
     *                   "description": "Dormouse.",
     *                   "field": "deaths",
     *                   "threshold": 0
     *               },
     *               {
     *                   "id": 3,
     *                   "name": "Oh dear!.",
     *                   "description": "On which.",
     *                   "field": "boss3lvl",
     *                   "threshold": 8
     *               }
     *           ]
     *       }
     *    @apiVersion 0.1.0
     */

         /**
     * @api {delete} /user/:id Delete user
     * @apiParam {Number} id Id of user to be deleted
     * @apiGroup User
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated User making the request is not logged in or has outdated access token.
     * @apiError InvalidAbilityProvided The user is not authorized to delete users.
     * @apiError NoQueryResultsForModel:id User with <code>id</code> could not be found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *     {
     *       "message": "Unauthenticated."
     *     }
     * @apiPermission admin
     * @apiSuccess {String} message Information about the user deletion.
     *    @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *     {
     *         "message": "User deleted successfully",
     *     }
     *    @apiVersion 0.1.0
     */

         /**
     * @api {delete} /user/restore/:id Restore user
     * @apiParam {Number} id Id of user to be restored
     * @apiGroup User
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated User making the request is not logged in or has outdated access token.
     * @apiError InvalidAbilityProvided The user is not authorized to restore deleted users.
     * @apiError NoQueryResultsForModel:id User with <code>id</code> could not be found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *     {
     *       "message": "Unauthenticated."
     *     }
     * @apiPermission admin
     * @apiSuccess {String} message Information about the user restoration.
     * @apiSuccess {Object} user Data of restored user
     * @apiSuccess {Number} user.id Id of restored user
     * @apiSuccess {Number} user.privilege <code>privilege</code> of restored user
     *    @apiSuccessExample {json} Success-Response:
     *   {
     *       "message": "User restored successfully",
     *       "user": {
     *           "id": 6,
     *           "privilege": 1
     *       }
     *   }
     *    @apiVersion 0.1.0
     */


 /**
  * @api {delete} /post/:id Delete post
  * @apiDescription Deleting posts, normal users can delete their own posts, while admis can delete everyone's.
  * @apiParam {Number} id Id of the post to be deleted
  * @apiGroup Post
  * @apiUse HeadersWithToken
  * @apiError Unauthenticated User making the request is not logged in or has outdated access token.
  * @apiError NoQueryEesultsForModel:id Post with <code>id</code> could not be found.
  * @apiError ActionNotAllowed Normal users are not allowed to delete others user's posts
  * @apiErrorExample {json} Error-Response:
  *     HTTP/1.1 401 Unauthorized
  *       {
  *           "message": "Action not allowed",
  *       }
  * @apiPermission normal user
  * @apiSuccess {String} message Information about the post deletion.
  * @apiSuccessExample {json} Success-Response:
  *    HTTP/1.1 200 OK
  *       {
  *           "message": "Post deleted successfully",
  *       }
  *    @apiVersion 0.1.0
  */

     /**
     * @api {delete} /post/restore/:id Restore post
     * @apiParam {Number} id Id of the post to be restored
     * @apiGroup Post
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated User making the request is not logged in or has outdated access token.
     * @apiError NoQueryEesultsForModel:id Post with <code>id</code> could not be found.
     * @apiError ActionNotAllowed Normal users are not allowed to restore posts
     * @apiError InvalidAbilityProvided The user is not authorized to restore posts
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unauthorized
     *       {
     *           "message": "Action not allowed",
     *       }
     * @apiPermission admin
     * @apiSuccess {String} message Information about the post restoration.
     * @apiSuccess {Object} post Data of the restored post.
     * @apiSuccess {Number} post.id <code>id</code> of the restored post.
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *           "message": "Post restored successfully",
     *           "post": {
     *               "id": 3
     *           }
     *       }
     *    @apiVersion 0.1.0
     */

         /**
     * @api {get} /post/:id Get post data
     * @apiDescription Getting post data, admin users get additional fields returned in the response, compared to normal users
     * @apiParam {Number} id Id of post to be queried
     * @apiGroup Post
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated. User making the request is not logged in or has outdated access token.
     * @apiError NoQueryResultsForModel:id Post with <code>id</code> could not be found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *       {
     *           "message": "Unauthenticated,"
     *       }
     * @apiPermission normal user
     * @apiSuccess (Success-Normal user) {Object} post Data of the requested post.
     * @apiSuccess (Success-Normal user) {Number} post.id   Post's <code>id</code>.
     * @apiSuccess (Success-Normal user) {String} post.post Post's <code>text</code>.
     * @apiSuccess (Success-Normal user) {String} post.image Post's <code>image</code> encoded with base64 encoding.
     * @apiSuccess (Success-Normal user) {Number} post.likes Post's number of <code>likes</code>.
     * @apiSuccess (Success-Normal user) {Date} post.created_at When the <code>post</code> was created.
     * @apiSuccess (Success-Normal user) {Date} post.modified_at When the <code>post</code> was last modified.
     * 
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Object} post Data of the requested post
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} post.deleted_at When the post was deleted
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *           "post": {
     *               "id": 11,
     *               "post": "Yeah body, light weight",
     *               "image": "data:image/jpg;base64;<base64-encoded-image>",
     *               "likes": 0,
     *               "created_at": "2024-11-26T17:12:34.000000Z",
     *               "modified_at": "2024-11-26T17:12:34.000000Z"
     *           }
     *       }
     *    @apiVersion 0.1.0
     */

          /**
     * @api {put} /user/update/:id User update
     * @apiParam {Number} id Id of user to be updated
     * @apiGroup User
     * @apiDescription Updating the user data, normal users are only allowed to update their own data, while admin users can update everyone's
     * @apiUse HeadersWithToken
     * @apiBody {String{min:3}} [name] New name of user
     * @apiBody {Email} [email] New email of user
     * @apiBody {Number} [deaths] New number of <code>deaths</code> of the user
     * @apiBody {Number} [kills] New number of <code>kills</code> of the user
     * @apiBody {Number} [waves] New number of <code>waves</code> of user
     * @apiBody {Number} [boss1lvl] New <code>boss1lvl</code> of user
     * @apiBody {Number} [boss2lvl] New <code>boss2lvl</code> of user
     * @apiBody {Number} [boss3lvl] New <code>boss3lvl</code> of user
     * @apiError Unauthenticated User making the request is not logged in or has outdated access token.
     * @apiError TheNameFieldMustBeAtLeast3Characters. <code>name</code> field must be at least 3 characters.
     * @apiError TheEmailFieldMustBeAValidEmailAddress. <code>email</code> field must be a valid email address.
     * @apiError TheDeathsFieldMustBeANumber <code>deaths</code> field must be a number.
     * @apiError TheKillsFieldMustBeANumber <code>kills</code> field must be a number.
     * @apiError ThewavesFieldMustBeANumber <code>waves</code> field must be a number.
     * @apiError TheBoss1lvlFieldMustBeANumber <code>boss1lvl</code> field must be a number.
     * @apiError TheBoss2lvlFieldMustBeANumber <code>boss2lvl</code> field must be a number.
     * @apiError TheBoss3lvlFieldMustBeANumber <code>boss3lvl</code> field must be a number.
     * @apiError NoQueryResultsForModel:id User with <code>id</code> could not be found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 422 Unprocessable Content
     *       {
     *           "message": "The name field must be at least 3 characters.",
     *           "errors": {
     *               "name": [
     *                   "The name field must be at least 3 characters."
     *               ]
     *           }
     *       }
     * @apiPermission normal user
     * @apiSuccess {String} message Information about the update.
     * @apiSuccess {Object} user Data of the updated user.
     * @apiSuccess {Number} user.id   User's <code>id</code>.
     * @apiSuccess {Number} user.name User's <code>name</code>.
     * @apiSuccess {Number} user.email User's <code>email</code>.
     * @apiSuccess {Number} user.deaths User's <code>deaths</code>.
     * @apiSuccess {Number} user.kills User's <code>kills</code>.
     * @apiSuccess {Number} user.waves User's <code>waves</code>.
     * @apiSuccess {Number} user.boss1lvl User's <code>boss1lvl</code>.
     * @apiSuccess {Number} user.boss2lvl User's <code>boss2lvl</code>.
     * @apiSuccess {Number} user.boss3lvl User's <code>boss3lv</code>l.
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *       "message": "User updated successfully",
     *          "user": {
     *              "id": 1,
     *              "name": "New name",
     *              "email": "test3@newemail.com",
     *              "deaths": 619,
     *              "kills": 4,
     *              "waves": 5,
     *              "boss1lvl": 4,
     *              "boss2lvl": 1,
     *              "boss3lvl": 7
     *          }
     *       }
     *    @apiVersion 0.1.0
     */