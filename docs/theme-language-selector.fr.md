# Menu déroulant de langue pour les thèmes

Extended Translation fournit un menu déroulant Bootstrap 5 qui permet aux visiteurs de changer la langue du site **sans quitter la page en cours**. Les thèmes peuvent l’inclure n’importe où (barre de navigation, pied de page, colonne latérale) au lieu de renvoyer vers la page de langue du plugin.

La page dédiée (`extended-translation.language`) existe toujours. Vous pouvez l’ajouter à la barre de navigation Azuriom si vous préférez un sélecteur pleine page. La plupart des thèmes devraient utiliser le menu déroulant.

## Prérequis

- Le plugin **Extended Translation** doit être activé.
- Au moins **deux langues** doivent être cochées dans **Admin → Traductions → Paramètres**. S’il y a moins de deux langues, le menu ne s’affiche pas.
- La mise en page doit charger le JavaScript Bootstrap 5 (`bootstrap.bundle.min.js`), ce qu’Azuriom fait déjà.

## Utilisation de base

Entourez l’inclusion avec la directive Azuriom `@plugin` pour que le thème fonctionne encore si le plugin est désactivé :

```blade
@plugin('extended-translation')
    @include('extended-translation::dropdown')
@else
    {{-- Repli facultatif lorsque le plugin est désactivé --}}
@endplugin
```

`extended-translation::selector` est un alias de la même vue (conservé pour les thèmes plus anciens).

## Exemple dans la barre de navigation

Placez le menu dans une liste d’utilitaires à droite, à côté de Connexion / Inscription :

```blade
<ul class="navbar-nav ms-auto align-items-center">
    <li class="nav-item">
        @plugin('extended-translation')
            @include('extended-translation::dropdown')
        @endplugin
    </li>

    {{-- liens d’authentification… --}}
</ul>
```

Le choix d’une langue enregistre la locale dans un cookie / la session, puis redirige vers l’URL courante.

## Paramètres facultatifs

Passez un tableau en second argument de `@include` :

| Paramètre     | Défaut                          | Description |
|---------------|---------------------------------|-------------|
| `align`       | `'end'`                         | `'end'` ajoute `dropdown-menu-end` (aligné à droite). Utilisez `'start'` pour aligner à gauche. |
| `toggleClass` | `''`                            | Classes CSS supplémentaires sur le bouton (fusionnées avec `et-lang-toggle dropdown-toggle`). |
| `menuClass`   | `''`                            | Classes CSS supplémentaires sur le formulaire du menu (fusionnées avec `dropdown-menu et-lang-menu`). |
| `redirect`    | URL complète courante           | URL de retour après le changement. Doit appartenir au même site. |
| `etRedirect`  | Identique à `redirect`          | Autre nom pour `redirect`. |

Exemple avec des classes propres au thème :

```blade
@include('extended-translation::dropdown', [
    'align' => 'end',
    'toggleClass' => 'my-lang-toggle',
    'menuClass' => 'my-lang-menu',
])
```

## Balisage et classes CSS

Les styles par défaut se chargent automatiquement via `plugin_asset('extended-translation', 'css/dropdown.css')`. Surchargez-les dans le CSS de votre thème.

| Classe              | Élément |
|---------------------|---------|
| `.et-lang-dropdown` | Conteneur (`div.dropdown`) |
| `.et-lang-toggle`   | Bouton qui affiche le code court actuel (`EN`, `FR`, …) |
| `.et-lang-menu`     | `<form>` qui est aussi le `.dropdown-menu` Bootstrap |
| `.et-lang-option`   | Lien pour chaque locale (aussi `.dropdown-item`) |
| `.et-lang-code`     | Code court de la locale |
| `.et-lang-name`     | Nom de la langue localisé |
| `.active`           | Option de la locale actuelle (`aria-current="true"`) |

Le bouton utilise `data-bs-toggle="dropdown"`. Chaque option est un lien vers `extended-translation.language.switch`.

Exemple de surcharge minimale :

```css
.et-lang-toggle {
    border-color: #c4a24a;
    letter-spacing: 0.15em;
}

.et-lang-code {
    color: #c4a24a;
}
```

## Page de langue (facultative)

Si vous voulez encore une page complète, ajoutez la route **Langue** dans **Admin → Barre de navigation**. Le nom de la route est `extended-translation.language`. Les visiteurs qui y arrivent peuvent choisir une langue et revenir à la page précédente.

Les thèmes qui utilisent le menu déroulant n’ont pas besoin de cet élément de menu.

## Traductions

Les textes affichés aux visiteurs se trouvent dans le plugin :

- `extended-translation::messages.switch` — `aria-label` du bouton
- `extended-translation::messages.title` — `aria-label` du menu

Les noms de langues viennent de la traduction Azuriom `messages.lang` de chaque locale.
