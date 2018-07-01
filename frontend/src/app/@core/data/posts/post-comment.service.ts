import { HttpClient } from "@angular/common/http";
import { Inject, Injectable } from "@angular/core";
import { BaseService } from "@core/data/base.service";
import { Post } from "./post.model";

import { Observable } from "rxjs/Observable";
import { catchError, map } from "rxjs/operators";

@Injectable()
export class PostCommentService extends BaseService {
	baseUrl   = "posts";
	modelName = "comments";

	constructor ( @Inject(HttpClient) http: HttpClient ) {
		super(http);

		this.model = ( construct: any ) => new Post(construct);
	}

	createForPost ( postId: number, body: any ): Observable<any> {
		return this.http.post(this._commentUrl(postId), body, { observe : "response" })
				   .pipe(
						   map(( res: any ) => this.mapModel(res.body)),
						   catchError(( err: any ) => Observable.throw(this.mapError(err))),
				   );
	}

	/**
	 * Build the complete url for API calls.
	 *
	 * @param {number} postId
	 *
	 * @return {string}
	 * @protected
	 */
	protected _commentUrl ( postId: number ): string {
		return `${this.baseUrl}/${postId}/${this.modelName}`;
	}
}