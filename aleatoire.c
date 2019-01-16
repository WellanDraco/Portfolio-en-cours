#include <stdio.h>
#include <stdlib.h>
#include <time.h>
#include <string.h>
#include <assert.h>

#define NB_EXPES 1000
#define TAILLE_MINI 100
#define NB_LISTES 10
#define NB_TRI 3


typedef struct stats {
	int nb_comp;
	int nb_permut;
	char* nom;
} Stats;

Stats matrice[NB_LISTES][NB_TRI];


/**typedef *pointeurFonc(int* int);*/

int* aleatoire(int tailleListe) {
	
	int* tab = (int*) malloc(tailleListe * sizeof(int) );
	int i;
	
	srand(time(NULL));
	
	for(i=0; i < tailleListe; i++){
		
		tab[i] = rand() %1000;
		
	}
	return tab;
}

void affiche(Stats* maStat) {
	
	printf("pour %s : il y  a %d comparaisons et %d permutations. \n", maStat->nom,maStat->nb_comp,maStat->nb_permut);
		
}

void test(int* tab, int tailleListe){
	int i;
	
	for(i=0; i< tailleListe - 1; i++){
		
		if(tab[i] > tab[i+1]) printf("PAS TRIE\n");;
		
	}
	printf("liste triée\n");
}

void permute(int* a, int*b){
	
	int temp;
	
	temp = *a;
	
	*a = *b;
	
	*b = temp;
	
}

void inserer(int insertPoint, int indiceB, int* tab, Stats* maStat){
	int i;
	
	if(insertPoint > indiceB) {
		
		permute(&insertPoint, &indiceB);
		maStat->nb_comp++;
	}
	
	for (i = indiceB; i>insertPoint;i--){
		
		permute(&tab[i], &tab[i-1]);
		maStat->nb_permut++;
	}
	
}

void triselection(int* tab, int tailleListe, Stats* maStat){
	
	int i, PPetite, j;
		
	for (i = 0; i < tailleListe - 1; i++) {
		
		PPetite = i;
		
		for (j = i; j < tailleListe; j++) {
			
			if (tab[j] < tab[PPetite]) {
				maStat->nb_comp++;
	
				PPetite = j;
				
			}
		}
		
		permute(tab+i, tab+PPetite);
		maStat->nb_permut++;
	}
}

void triInsert(int* tab, int tailleListe, Stats* maStat) {
	
	int indiceB;
	int insertPoint;
	
	for (indiceB = 1; indiceB < tailleListe;indiceB++) {
	
		int i = 0;
		
		while ((i < indiceB) && (tab[indiceB] > tab[i])) {
			maStat->nb_comp++;
			i++;
			
		}
		
		insertPoint = i;
		
		inserer(insertPoint,indiceB, tab, maStat);
		
	}
	
}

void triBulle(int*tab, int tailleListe, Stats* maStat) {
	
	int i, asrotated;
	asrotated = 1;
	
	while (asrotated > 0) {
		
		asrotated = 0;
		
		for (i=0 ;i < tailleListe - 1;i++) {
		
			if (tab[i] > tab[i+1]) {
				
				maStat->nb_comp++;
				
				permute(tab+i, tab+i+1);
				maStat->nb_permut++;
				
				asrotated++;
				
			}
			
		}
		
	}
	
}

void traitement(int cpt, int t) {
		
	/** Partie Tri selection, insertion et bulles.*/
	int * tab;
	int taille = t;
	int colonneTaille = t/TAILLE_MINI;
	int * tabS = (int *) malloc(taille * sizeof(int));
	
	tab = aleatoire(taille);
	
	matrice[colonneTaille][0].nb_comp = 0;
	matrice[colonneTaille][0].nb_permut = 0;
	matrice[colonneTaille][0].nom = "triselection";
	matrice[colonneTaille][1].nb_comp = 0;
	matrice[colonneTaille][1].nb_permut = 0;
	matrice[colonneTaille][1].nom = "triInsert";
	matrice[colonneTaille][2].nb_comp = 0;
	matrice[colonneTaille][2].nb_permut = 0;
	matrice[colonneTaille][2].nom = "triBulle";
	
	memcpy(tabS,tab,taille*sizeof(int));
	triselection(tabS, taille, &matrice[colonneTaille][0]); /**utiliser l'adresse*/
	affiche( &matrice[colonneTaille][0]);
	test(tabS, taille);
	
	memcpy(tabS,tab, taille*sizeof(int));
	triInsert(tabS, taille, &matrice[colonneTaille][1]);
	affiche( &matrice[colonneTaille][1]);
	test(tabS, taille);
	
	
	memcpy(tabS,tab, taille*sizeof(int));
	triBulle(tabS, taille, &matrice[colonneTaille][2]);
	affiche( &matrice[colonneTaille][2]);
	test(tabS, taille);		
	
	free(tabS);
	free(tab); 
	
	return;
}

void compute(){
	/**t est la taille de chaque fonction
	 * cpt est le nombre de fonction par taille
	*/
	int t,cpt;
	
	for (t = TAILLE_MINI; t <= TAILLE_MINI*NB_LISTES; t = t+TAILLE_MINI){
		
		for(cpt = 0; cpt < NB_EXPES; cpt++){
			
			traitement(cpt,t);
			
		}
	}
	printf("\nFin de generation de liste\n");
}

int main (void) {
	/**pointeurFonc[] = {triselection,triInsert,triBulle};
	//FILE * myfile = fopen("file.csv","w");*/
	
	compute();
	
	
return 0;
}
