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
     * @apiSuccess (Success-Normal user) {Number} user.points User's <code>points</code>.
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
     *               "points": 5,
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