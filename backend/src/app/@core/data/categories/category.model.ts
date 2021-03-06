import { CategoryLang } from "./category-lang.model";

/**
 *
 */
export class Category {
	public id: number;
	public is_active: number            = 0;
	public translations: CategoryLang[] = [];
	public created_on: string;
	public updated_on: string;

	constructor ( model: any = null ) {
		if (!model) { return; }

		this.id         = model.id;
		this.is_active  = model.is_active;
		this.created_on = model.created_on;
		this.updated_on = model.updated_on;

		this.translations = this.mapTranslations(model.translations);
	}

	/**
	 *
	 * @param list
	 * @return {CategoryLang[]}
	 */
	mapTranslations ( list: any[] ): CategoryLang[] {
		list.forEach(( val, idx ) => {
			list[ idx ] = new CategoryLang(val);
		});

		return list;
	}

	/**
	 *
	 * @param {number | string} lang
	 *
	 * @return {CategoryLang}
	 */
	findTranslation ( lang: number | string ) {
		let result = new CategoryLang();

		if (!this.translations) {
			return result;
		}

		this.translations.forEach(( val ) => {
			if (typeof lang === "string" && val.language === lang) {
				result = val;
			}

			if (typeof lang === "number" && val.lang_id === lang) {
				result = val;
			}
		});

		return result;
	}

	firstTranslation (): CategoryLang {
		return this.translations[ 0 ];
	}

	/**
	 *
	 * @param model
	 */
	form ( model: any ): any {
		return {
			is_active    : (model.is_active) ? 1 : 0,
			translations : this.mapFormTranslations(model.translations),
		};
	}

	isActive () { return (this.is_active === 1); }

	isInactive () { return (this.is_active === 0); }

	/**
	 *
	 * @param list
	 * @return {CategoryLang[]}
	 */
	mapFormTranslations ( list: any ): CategoryLang[] {
		const result: CategoryLang[] = [];

		list.forEach(( val ) => {
			if (val.name || val.slug) {
				result.push(new CategoryLang(val, this.id));
			}
		});

		return result;
	}
}
