
insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'Reconnaissance vs satisfaction',
	'Quelle réussite récente t''a apporté plus de reconnaissance que de vraie satisfaction intérieure ?',
	NOW(),
	(select id from sub_theme_capsule where id = 4 )
);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'La logique vs Le juste',
	'Qu''est-ce que tu poursuis encore parce que “c''est logique”, pas parce que c''est juste pour toi ?',
	NOW(),
	(select id from sub_theme_capsule where id = 4 )
);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'Le regard des autres',
	'Si personne ne regardait, qu''est-ce que tu ferais différemment dans ton travail ?',
	NOW(),
	(select id from sub_theme_capsule where id = 4 )
);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'Le succès vide',
	'Qu''est-ce qui te rend fièr(e) sur le papier mais vide à l''intérieur ?',
	NOW(),
	(select id from sub_theme_capsule where id = 4 )
);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'Le CV émotionnel',
	'Si ton/ta meilleur(e) ami(e) écrivait ton CV émotionnel, quel talent de cœur mettrait-il/elle en premier ?',
	NOW(),
	(select id from sub_theme_capsule where id = 5 )
);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'Le talent invisible',
	'Qu''est-ce que tu fais naturellement bien mais que tu minimises systématiquement ?',
	NOW(),
	(select id from sub_theme_capsule where id = 5 )
);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'L''objectivité ',
	'Dans quelle situation récente t''es-tu sentie “pas assez”, alors que tu étais objectivement compétent(e) ?',
	NOW(),
	(select id from sub_theme_capsule where id = 5 )
);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'La norme',
	'Quelle qualité chez toi est devenue invisible parce que tu la considères comme “normale” ?',
	NOW(),
	(select id from sub_theme_capsule where id = 5 )
);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'L''impressionnant',
	'Qu''est-ce que tu accomplis sans effort que d''autres trouvent impressionnant ?',
	NOW(),
	(select id from sub_theme_capsule where id = 5 )
);


insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'Poursuite ou Fuite',
	'Est-ce que tu poursuis cet objectif ou est-ce que tu fuis une peur plus ancienne ?',
	NOW(),
	(select id from sub_theme_capsule where id = 6 )
);


insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'Le ralentissement réel',
	'Qu''est-ce qui se passerait si tu ralentissais vraiment, pas juste en apparence ?',
	NOW(),
	(select id from sub_theme_capsule where id = 6 )
);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'Le stop du corps',
	'Quelle peur te pousse à continuer même quand ton corps dit stop ?',
	NOW(),
	(select id from sub_theme_capsule where id = 6 )
);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'Au-delà de l''image',
	'Si tu arrêtais de prouver, qu''est-ce qui resterait de ton ambition ?',
	NOW(),
	(select id from sub_theme_capsule where id = 6 )
);
insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'Le désir profond',
	'Qu''est-ce que tu veux profondément… au-delà du statut et des résultats ?',
	NOW(),
	(select id from sub_theme_capsule where id = 6 )
);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'Le fantasme de la fuite',
	'Si tu pouvais tout laisser là, maintenant (maison, job, famille), est-ce que tu aurais peur ou est-ce que tu serais soulagé(e) ?',
	NOW(),
	(select id from sub_theme_capsule where id = 7 )
	);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'La fonctionnaire de sa propre vie',
	'Es-tu devenu(e) un(e) simple exécutant(e) de tes propres choix d''autrefois ?',
	NOW(),
	(select id from sub_theme_capsule where id = 7 )
	);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'Le bruit pour ne pas entendre',
	'Pourquoi as-tu besoin d''un podcast, de la télé ou de musique en permanence pour ne pas rester seul(e) avec tes pensées ?',
	NOW(),
	(select id from sub_theme_capsule where id = 7 )
	);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'Le/la "Moi" oublié(e) sous les rôles',
	'Si on t''enlevait tes titres (Mère/père, Époux.se, Poste), qu''est-ce qui ferait encore briller tes yeux dans le silence ?',
	NOW(),
	(select id from sub_theme_capsule where id = 7 )
	);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'L''illusion du "Quand j''aurai..."',
	'Quelle est cette chose que tu attends pour t''autoriser à être heureux.se, et si elle n''arrivait jamais, que ferais-tu aujourd''hui ?',
	NOW(),
	(select id from sub_theme_capsule where id = 7 )
	);
	
insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'Le décalage entre tes valeurs et tes heures',
	'Regarde ton agenda : quelle place reste-t-il pour ce qui te fait vraiment te sentir vivant(e), et pas juste utile ?',
	NOW(),
	(select id from sub_theme_capsule where id = 7 )
	);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'Le corps en attente',
	'Est-ce que tu habites encore ton corps aujourd''hui, ou est-ce que tu le considères seulement comme un instrument qui "ne marche pas encore" ?',
	NOW(),
	(select id from sub_theme_capsule where id = 8 )
	);
insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'Le désir vs La performance',
	'À quel moment le désir de donner la vie est-il devenu une mission de performance qui t''épuise ?',
	NOW(),
	(select id from sub_theme_capsule where id = 8 )
	);
insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'L''éclipse sociale',
	'Quelle part de ta vie as-tu mise "sur pause" en attendant cet enfant, et qui te manque profondément là maintenant ?',
	NOW(),
	(select id from sub_theme_capsule where id = 8 )
	);
insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'La jalousie taboue',
	'Face au bonheur des autres, quelle émotion t''interdis-tu de ressentir pour rester une "belle personne" ?',
	NOW(),
	(select id from sub_theme_capsule where id = 8 )
	);
insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'Le duo sous pression',
	'Dans votre couple, est-ce qu''il reste un espace où vous n''êtes pas des "futurs parents" mais juste deux amants ?',
	NOW(),
	(select id from sub_theme_capsule where id = 8 )
	);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'Le corps dépossédé',
	'Qu''est-ce qui est le plus difficile à accepter : que son/ton corps change, ou qu''il ne lui/t''appartienne plus tout à fait en ce moment ?',
	NOW(),
	(select id from sub_theme_capsule where id = 9 )
	);
insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'L''épuisement sacré',
	'Si tu n''avais pas besoin d''être ce ''super-parent'' qui gère tout, quel cri ton corps pousserait-il là, tout de suite ?',
	NOW(),
	(select id from sub_theme_capsule where id = 9 )
	);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'Le brouillard du 4ème trimestre',
	'Entre les soins, les pleurs et les nuits, où es-tu passé(e) ? Arrives-tu à te retrouver 5 minutes par jour ?',
	NOW(),
	(select id from sub_theme_capsule where id = 9 )
	);
insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'La douleur silencieuse',
	'Quelle douleur minimises-tu chaque jour en te disant que ''c''est normal, c''est le métier qui rentre''?',
	NOW(),
	(select id from sub_theme_capsule where id = 9 )
	);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'L''idéal qui s''effondre',
	'Quelle image de la parentalité es-tu en train de laisser mourir pour pouvoir enfin accepter ta réalité ?',
	NOW(),
	(select id from sub_theme_capsule where id = 9 )
	);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'Le lien en construction',
	'Est-ce que tu t''en veux de ne pas ressentir ce ''coup de foudre immédiat'', alors que l''amour est parfois un chemin lent ?',
	NOW(),
	(select id from sub_theme_capsule where id = 9 )
	);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'Le miroir déformant',
	'Quelle réaction de ton enfant te renvoie directement à un trait de caractère que tu n''aimes pas chez toi ?',
	NOW(),
	(select id from sub_theme_capsule where id = 10 )
	);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'L''espace vide',
	'Maintenant qu''ils ont moins besoin de tes mains, de quoi as-tu peur de remplir ce nouveau temps libre ?',
	NOW(),
	(select id from sub_theme_capsule where id = 10 )
	);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'La nostalgie utile',
	'Est-ce que tu regrettes l''époque où ils étaient petits, ou est-ce que tu regrettes la personne que tu étais à ce moment-là ?',
	NOW(),
	(select id from sub_theme_capsule where id = 10 )
	);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'Le contrôle déguisé en conseil',
	'Dans ta dernière remarque à ton enfant, cherchais-tu vraiment à l''aider ou cherchais-tu à te rassurer ?',
	NOW(),
	(select id from sub_theme_capsule where id = 10 )
	);

insert into capsule(
	title,
	content,
	created_at,
	sub_theme_capsule_id
)
values (
	'La transmission silencieuse',
	'Si ton enfant devenait exactement comme toi aujourd''hui, en serais-tu fier(e) ou inquiet(e) ?',
	NOW(),
	(select id from sub_theme_capsule where id = 10 )
	);