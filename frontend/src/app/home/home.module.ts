import { NgModule } from "@angular/core";
import { CommonModule } from "@angular/common";
import { DirectivesModule } from "../@shared/directives/directives.module";

import { HomeRoutingModule } from "./home-routing.module";
import { HomeComponent } from "./home.component";
import { FeaturedPostComponent } from './featured-post/featured-post.component';

@NgModule({
	imports      : [
		CommonModule,
		DirectivesModule,
		HomeRoutingModule,
	],
	declarations : [
		HomeComponent,
		FeaturedPostComponent,
	],
})
export class HomeModule {
}